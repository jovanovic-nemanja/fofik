<?php

namespace App\Services;

use Closure;
use DB;

use Google\Cloud\Core\ServiceBuilder;
use Aws\Rekognition\RekognitionClient;

use WikitextParser;
use PHPHtmlParser\Dom;
use DiDom\Document;

class CloudApiService extends BaseService
{

    protected $mapLangCode;
    public function __construct()
    {
        $this->mapLangCode = array (
            'en' => 'US', 
            'tr' => 'TR', 
            'fr' => 'FR', 
            'de' => 'DE', 
            'gb' => 'GB', 
            'se' => 'SE', 
        );
    }   

    public function googleCV($photo)
    {
        // API URL
        $url = 'https://vision.googleapis.com/v1p4beta1/images:annotate?key=AIzaSyCZIfP_mQ6-EQzEB_ECRqxqjQQCmiIVJUA';

        // Create a new cURL resource
        $ch = curl_init($url);

        //Getting image
        // $image = file_get_contents('https://pbs.twimg.com/profile_images/988775660163252226/XpgonN0X_400x400.jpg');
        $image = file_get_contents(public_path($photo));
        //converting image into base64
        $image_64= base64_encode($image);

        // Setup request to send json via POST
        $data = [
            "requests" => [
                [
                    "image" => [
                    "content" => $image_64 
                    ], 
                    "features" => [
                        [
                            "type" => "FACE_DETECTION" 
                        ] 
                    ], 
                    "imageContext" => [
                        "faceRecognitionParams" => [
                            "celebritySet" => [
                                "builtin/default" 
                            ] 
                        ] 
                    ] 
                ] 
            ] 
        ]; 
    
        $payload = json_encode($data);

        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the POST request
        $result = curl_exec($ch);
        // Close cURL resource
        curl_close($ch);

        $result = json_decode($result);
        $faces = @$result->responses[0]->faceAnnotations;
        if ($faces) {
            $celeb = $faces[0];
            $candidates = @$celeb->recognitionResult;
            if ($candidates && count($candidates) > 0) {
                $accuracy = 0; $top = 0;
                foreach ($candidates as $itr => $each) {
                    if ($each->confidence > $accuracy) {
                        $accuracy = $each->confidence;
                        $top = $itr;
                    }
                }
                return $candidates[$top]->celebrity->displayName;
            }
        }
        return null;
    }
    public function amazonCV($photo)
    {
        $client = new RekognitionClient([
            'region'    => 'eu-central-1',
            'version'   => 'latest'
        ]);

        $image = fopen(public_path($photo), 'r');
        $bytes = fread($image, filesize(public_path($photo)));
        $result = $client->recognizeCelebrities(['Image' => ['Bytes' => $bytes]]);
        $celebs = $result["CelebrityFaces"];
        if (count($celebs) > 0) {
            $confidence = 0; $top = 0;
            foreach ($celebs as $itr => $each) {
                if ($each['MatchConfidence'] > $confidence) {
                    $top = $itr;
                    $confidence = $each['MatchConfidence'];
                }
            }
            return $celebs[$top]['Name'];
        }
        return null;
    }
    public function youtube($params)
    {
        $name = $params['name'];
        $lang = $params['lang'];
        $apikey = 'AIzaSyCZIfP_mQ6-EQzEB_ECRqxqjQQCmiIVJUA'; 
        // $googleApiUrl = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q=' . str_replace(' ', '%20' , name) . '&maxResults=' . 10 . '&key=' . $apikey;

        $url = 'https://www.googleapis.com/youtube/v3/search?key='. $apikey;
        $payload = array (
            'part' => 'snippet',
            'q' => str_replace(' ', '%20', $name),
            'maxResults' => 20,
            // 'regionCode' => 'TR',
            'relevanceLanguage' => $lang
        );
        $res = $this->api($url, $payload);
        $data = [];
        foreach ($res->items as $item) {
            if ($item->id->kind == 'youtube#video') {
                $data[] = array (
                    'video_id' => $item->id->videoId,
                    'title' => $item->snippet->title,
                    'summary' => $item->snippet->description,
                    'image' => $item->snippet->thumbnails
                );
            }
        }
        return $data;
    }
    public function imdb($id)
    {
        $apiKey = '714ccbfe04acc9c6ec4e8b1e31f09d79';
        $url = 'https://api.themoviedb.org/3/movie/'.$id.'/external_ids?';
        $payload = array (
            'api_key' => $apiKey,
        );
        $res = $this->api($url, $payload);
        $imdbID = $res->imdb_id;
        return array (
            'imdb_id' => $imdbID,
            'link' => 'https://www.imdb.com/title/'.$imdbID
        );
    }
    public function tmdb($params)
    {
        $name = $params['name'];
        $lang = $params['lang'];
        $apiKey = '714ccbfe04acc9c6ec4e8b1e31f09d79';
        $url = 'https://api.themoviedb.org/3/search/person?';
        $payload = array (
          'api_key' => $apiKey,
          'language' => isset($this->mapLangCode[$lang]) ? $lang.'-'.$this->mapLangCode[$lang] : 'en-US',
          'query' => str_replace(' ', '+', $name),  
        );  
        $res = $this->api($url, $payload);
        if (count($res->results) == 0)
            return null;
        $pID = $res->results[0]->id;
        $url = 'https://api.themoviedb.org/3/person/'.$pID.'/movie_credits?';
        unset($payload['query']);
        $res = $this->api($url, $payload);
        foreach ($res->cast as $movie)
        {
            $data[] = array (
                'id' => $movie->id,
                'title' => $movie->title,
                'poster_url' => 'https://image.tmdb.org/t/p/original/'.$movie->poster_path
            );
        }
        return $data;
    }
    public function wikiBase($params)
    {        
        $name = @$params['name'];
        $lang = @$params['lang'];

        if (!$name)
            return null;
        // $lang = 'en';
        $url = 'https://www.wikidata.org/w/api.php?';
        $payload = array (
            'action' => 'wbgetentities',
            'languages' => $lang.'|en',
            'format' => 'json',
            'titles' => str_replace(' ', '%20', $name),
            'sites' => 'enwiki|trwiki',
        );
        $res = $this->api($url,$payload);
        if (!$res)
            return null;
        $wikidata = null;
        foreach ($res->entities as $item) {
            $wikidata = $item;
        }

        $data = [];
        $entities = [];
        
        if (!@$wikidata->labels) 
            return null;
        
        $data['external_id'] = $wikidata->title;
        $data['en_name'] = $wikidata->labels->{'en'}->value;
        $data['natl_name'] = $wikidata->labels->{$lang}->value;
        $data['comment'] = $wikidata->descriptions->{$lang}->value;

        $p_image = 'P18';
        $p_citizenship = 'P27';
        $p_birthdate = 'P569';
        $p_deathdate = 'P570';
        $p_birthplace = 'P19';
        $p_starttime = 'P580';
        $p_endtime = 'P582';
        $p_spouse = 'P26';
        $p_child = 'P40';
        $p_occupation = 'P106';
        $p_educated_at = 'P69';
        $p_networth = 'P2218';
        $p_facebook = 'P2013';
        $p_instagram = 'P2003';
        $p_twitter = 'P2002';


        if (@$wikidata->claims->{$p_image}) {
            $imgExt = $wikidata->claims->{$p_image}[0]->mainsnak->datavalue->value;
            $hash = md5($imgExt);
            $data['photo_url'] = 'https://upload.wikimedia.org/wikipedia/commons/'.$hash[0].'/'.$hash[0].$hash[1].'/'.$imgExt;
        }
        if (@$wikidata->claims->{$p_citizenship}) {
            foreach ($wikidata->claims->{$p_citizenship} as $key => $item) {
                $q_citizenship = $item->mainsnak->datavalue->value->id;
                $entities[$q_citizenship] = array('field' => 'citizen_ship', 'mul' => true, 'key' => $key);
            }
        }
        if (@$wikidata->claims->{$p_birthdate}) {
            $data['birth_date'] = Date($wikidata->claims->{$p_birthdate}[0]->mainsnak->datavalue->value->time);
        }
        if (@$wikidata->claims->{$p_deathdate}) {
            $data['death_date'] = Date($wikidata->claims->{$p_deathdate}[0]->mainsnak->datavalue->value->time);
        }
        if (@$wikidata->claims->{$p_birthplace}) {
            $q_birthplace = $wikidata->claims->{$p_birthplace}[0]->mainsnak->datavalue->value->id;
            $entities[$q_birthplace] = array('field' => 'born_in', 'mul' => false);
        }
        if (@$wikidata->claims->{$p_spouse}) {
            foreach ($wikidata->claims->{$p_spouse} as $key => $spouse)
            {
                $q_spouse = $spouse->mainsnak->datavalue->value->id;
                $start_time = null; $end_time = null;
                if (@$spouse->qualifiers->{$p_starttime})
                    $start_time = $spouse->qualifiers->{$p_starttime}[0]->datavalue->value->time;
                if (@$spouse->qualifiers->{$p_endtime})
                    $end_time = $spouse->qualifiers->{$p_endtime}[0]->datavalue->value->time;
                $entities[$q_spouse] = array('field' => 'spouse', 'mul' => true, 'key' => $key, 'meta' => [@$start_time, @$end_time]);
            }
        }
        if (@$wikidata->claims->{$p_child}) {
            foreach ($wikidata->claims->{$p_child} as $key => $child)
            {
                $q_child = $child->mainsnak->datavalue->value->id;
                $entities[$q_child] = array('field' => 'child', 'mul' => true, 'key' => $key);
            }
        }
        if (@$wikidata->claims->{$p_occupation}) {
            foreach ($wikidata->claims->{$p_occupation} as $key => $occupation)
            {
                $q_occupation = $occupation->mainsnak->datavalue->value->id;
                $entities[$q_occupation] = array('field' => 'occupation', 'mul' => true, 'key' => $key);
            }
        }
        if (@$wikidata->claims->{$p_educated_at}) {
            foreach ($wikidata->claims->{$p_educated_at} as $key => $college)
            {
                $q_educated_at = $college->mainsnak->datavalue->value->id;
                $entities[$q_educated_at] = array('field' => 'education', 'mul' => true, 'key' => $key);
            }
        }   

        if (@$wikidata->claims->{$p_networth}) {
            $data['net_worth'] = $wikidata->claims->{$p_networth}[0]->mainsnak->datavalue->value->amount;
        }
        if (@$wikidata->claims->{$p_facebook}) {
            $data['facebook'] = $wikidata->claims->{$p_facebook}[0]->mainsnak->datavalue->value;
        }
        if (@$wikidata->claims->{$p_instagram}) {
            $data['instagram'] = $wikidata->claims->{$p_instagram}[0]->mainsnak->datavalue->value;
        }
        if (@$wikidata->claims->{$p_twitter}) {
            $data['twitter'] = $wikidata->claims->{$p_twitter}[0]->mainsnak->datavalue->value;
        }
        unset($payload['titles']);
        $entIds = [];
        foreach ($entities as $key => $value) 
        {
            $endIds[] = $key;
        }
        $payload['ids'] = implode("|", $endIds);
        $payload['props'] = 'labels';
        $res = $this->api($url, $payload);

        foreach ($res->entities as $entId => $entVal)
        {
            if (isset($entVal->labels->{$lang}))
            {
                $value = $entVal->labels->{$lang}->value;
                $field = $entities[$entId]['field'];
                $is_arr = $entities[$entId]['mul'];
                $meta = @$entities[$entId]['meta'] ? $entities[$entId]['meta'] : null;
                if (!$is_arr) {
                    $data[$field] = $value;
                } else {
                    if (!@$data[$field]) 
                        $data[$field] = [];
                    if ($meta)
                        $data[$field][] = array_merge(array($value), $meta);
                    else
                        $data[$field][] = $value;
                }
            }
        }
        $data['description'] = [];
        $document = new Document('https://'.$lang.'.wikipedia.org/wiki/'.str_replace(' ', '_', $name), true);
        $data['photo_url'] = $document->find('table.infobox .image img')[0]->getAttribute('src');
        $sub_headings = $document->find('h2');
        foreach($sub_headings as $sub_heading) {
            if ($sub_heading->parent()->getAttribute('class') == 'mw-parser-output' &&
                !str_contains($sub_heading->text(), "External links") &&
                !str_contains($sub_heading->text(), "Further reading") &&
                !str_contains($sub_heading->text(), "References") &&
                !str_contains($sub_heading->text(), "Legacy")) {
                $content = $sub_heading->html();
                $temp = $sub_heading->nextSibling();
                while ($temp && !$temp->has('h2')) {
                    $content .= $temp->html();
                    $temp = $temp->nextSibling();
                    if (!$temp || $temp->has('h2'))
                        break;
                }
                $content = str_replace('/wiki/', 'https://'.$lang.'.wikipedia.org/wiki/', $content);
                $data['description'][] = array (
                    'title' => $sub_heading->text(),
                    'content' => $content
                );
            }
        }
        $data['description'] = json_encode($data['description']);
        return $data;
    }   
    public function bingNews($params)
    {
        $name = @$params['name'];
        $lang = @$params['lang'];
        if (!$name)
            return [];
        $url = 'https://api.bing.microsoft.com/v7.0/news/search?';
        $payload = array (
            'q' => str_replace(' ',  '%20', $name),
            'count' => 20,
            'sortBy' => 'Date',
            // 'mkt' => 'tr-TR',
            'cc' => isset($this->mapLangCode[$lang]) ? $this->mapLangCode[$lang] : 'US',
            'since' => date_timestamp_get(date_create('2010-12-01')),
        );
        $apikey = '165205352171421bbaecc8e9dc49cc7d';
        $header = array(
            'Ocp-Apim-Subscription-Key:'.$apikey
        );
        $res = $this->api($url, $payload, $header);
        if (count($res->value) == 0) {
            $payload['cc'] = 'US';
            $res = $this->api($url, $payload, $header);
        }
        $data = [];
        foreach ($res->value as $news)
        {
            $data[] = array(
                'title' => $news->name,
                'summary' => $news->description,
                'post_url' => @$news->image ? $news->image->thumbnail->contentUrl : '',
                'link_url' => $news->url,
                'release_date' => $news->datePublished,
            );
        }
        return $data;
    }
    public function bingImages($params)
    {
        $name = @$params['name'];
        if (!$name)
            return [];
        $url = 'https://api.bing.microsoft.com/v7.0/images/search?';
        $payload = array (
            'q' => str_replace(' ',  '%20', $name),
            'count' => 80,
            'imageType' => 'Photo',
            'size' => 'medium',
            'imageContent' => 'Face'
        );
        $apikey = '165205352171421bbaecc8e9dc49cc7d';
        $header = array(
            'Ocp-Apim-Subscription-Key:'.$apikey
        );
        $data = [];
        $res = $this->api($url, $payload, $header);
        foreach ($res->value as $image)
        {
            $data[] = $image->contentUrl;
        }
        return $data;
    }
    public function bingEntities($keyword)
    {
        $url = 'https://api.bing.microsoft.com/v7.0/entities?';
        $payload = array (
            'q' => $keyword,
            'mkt' => 'en-US',
        );
        $apikey = '165205352171421bbaecc8e9dc49cc7d';
        $header = array(
            'Ocp-Apim-Subscription-Key:'.$apikey
        );
        $res = $this->api($url, $payload, $header);
        print_r($res); exit();
        return $data;
    }
    public function gNews($params)
    {
        
    }
    public function songkick($params)
    {
        
    }
    public function predictHQ($params)
    {
        $name = $params['name'];
        $lang = $params['lang'];
        $url = 'https://api.predicthq.com/v1/events?';
        $ACCESS_TOKEN = "kwSQdX9mVF_nFyorO8nAnDkYYP1qtSZPT0s9hiPd";
        $payload = array (
            'q' => str_replace(' ', '+', $name),
            'limit' => 10,
            'country' => isset($this->mapLangCode[$lang]) ? $this->mapLangCode[$lang] : 'US'
        );
        $header = array (
            "Authorization: Bearer $ACCESS_TOKEN",
            "Accept: application/json"
        );
        $res = $this->api($url, $payload, $header);
        if (count($res->results) == 0) {
            $payload['country'] = 'US';
            $res = $this->api($url, $payload, $header);
        }
        $data = [];
        foreach ($res->results as $event) {
            $data[] = array (
                'title' => $event->title,
                'description' => $event->description,
                'category' => $event->category,
                'duration' => $event->duration,
                'start' => $event->start,
                'end' => $event->end,
                'updated' => $event->updated,
                'first_seen' => $event->first_seen,
            );
        }
        return $data;
    }
    public function rapidBirth()
    {
        $url = 'https://celebrity-bucks.p.rapidapi.com/birthdays/JSON';
        $header = array (
            "x-rapidapi-key: 84ebadb67fmsh942141f601f4f73p13b353jsn74ce2c9cd19c",
            "x-rapidapi-host: celebrity-bucks.p.rapidapi.com",
            "useQueryString: true"
        );
        $res = $this->api($url, [], $header);
        $data = [];
        foreach ($res->Birthdays as $item)
        {
            $data[] = $item->name;
        }
        return $data;
    }
    public function api($url, $payload, $header = null)
    {
        foreach ($payload as $key => $value) {
            $url .= ('&'.$key.'='.$value);
        }
        $ch = curl_init($url);
        if (!$header)
            curl_setopt($ch, CURLOPT_HEADER, 0);
        else
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);
        return json_decode($response);
    }
}

