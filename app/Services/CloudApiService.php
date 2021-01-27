<?php

namespace App\Services;

use Closure;
use DB;

use Google\Cloud\Core\ServiceBuilder;
use Aws\Rekognition\RekognitionClient;

use WikitextParser;

class CloudApiService extends BaseService
{
    public function __construct()
    {

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

        $apikey = 'AIzaSyCZIfP_mQ6-EQzEB_ECRqxqjQQCmiIVJUA'; 
        // $googleApiUrl = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q=' . str_replace(' ', '%20' , name) . '&maxResults=' . 10 . '&key=' . $apikey;

        $url = 'https://www.googleapis.com/youtube/v3/search?key='. $apikey;
        $payload = array (
            'part' => 'snippet',
            'q' => str_replace(' ', '%20', $name),
            'maxResults' => 20,   
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
    public function imdb($params)
    {
        $name = $params['name'];
        $lang = $params['lang'];
        $lang = 'en';
        $apikey = 'k_eljtb34t';
        $url = 'https://imdb-api.com/'.$lang.'/API/SearchName/'.$apikey.'/'.str_replace(' ', '%20', $name);
        $res = $this->api($url, []);
        if (count($res->results) == 0)
            return null;
        $nmId = $res->results[0]->id;
        $url = 'https://imdb-api.com/en/API/Name/'.$apikey.'/'.$nmId;
        $res = $this->api($url, []);

        $start = 0;
        $length = 10;

        $movies = $res->castMovies;
        array_splice($movies, $start, $length);

        foreach ($movies as $movie)
        {
            $url = 'http://www.omdbapi.com/?&plot=full&apikey=b9b1735c&i='.$movie->id;
            $detail = $this->api($url, []);
            $data[] = array (
                'title' => @$detail->Title,
                'year' => @$detail->Year,
                'rated' => @$detail->Rated,
                'runtime' => @$detail->Runtime,
                'genre' => @$detail->Genre,
                'plot' => @$detail->Plot,
                'metascore' => @$detail->Metascore,
                'imdbrating' => @$detail->imdbRating,
                'imdbvotes' => @$detail->imdbVotes,
                'poster_url' => @$detail->Poster
            );
        }
        return $data;
    }
    public function gNews($params)
    {
        
    }
    public function songkick($params)
    {
        
    }
    public function wikiBase($params)
    {
        $name = $params['name'];
        $lang = $params['lang'];
        // $lang = 'en';
        $url = 'https://www.wikidata.org/w/api.php?';
        $payload = array (
            'action' => 'wbgetentities',
            'languages' => $lang.'|en',
            'format' => 'json',
            'titles' => str_replace(' ', '%20', $name),
            'sites' => 'enwiki',
        );
        $res = $this->api($url,$payload);
        print_r($res); exit();
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
        $data['description'] = $wikidata->descriptions->{$lang}->value;

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
            foreach ($wikidata->claims->{$p_citizenship} as $item) {
                $q_citizenship = $item->mainsnak->datavalue->value->id;
                $entities[$q_citizenship] = 'citizen_ship';
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
            $entities[$q_birthplace] = 'born_in';
        }
        if (@$wikidata->claims->{$p_spouse}) {
            foreach ($wikidata->claims->{$p_spouse} as $spouse)
            {
                $q_spouse = $spouse->mainsnak->datavalue->value->id;
                $entities[$q_spouse] = 'spouse';
                if (@$spouse->qualifiers->{$p_starttime})
                    $spouse->qualifiers->{$p_starttime}[0]->datavalue->value->time;
                if (@$spouse->qualifiers->{$p_endtime})
                    $spouse->qualifiers->{$p_endtime}[0]->datavalue->value->time;
            }
        }
        if (@$wikidata->claims->{$p_child}) {
            foreach ($wikidata->claims->{$p_child} as $child)
            {
                $q_child = $child->mainsnak->datavalue->value->id;
                $entities[$q_child] = 'child';
            }
        }
        if (@$wikidata->claims->{$p_occupation}) {
            foreach ($wikidata->claims->{$p_occupation} as $occupation)
            {
                $q_occupation = $occupation->mainsnak->datavalue->value->id;
                $entities[$q_occupation] = 'occupation';
            }
        }
        if (@$wikidata->claims->{$p_educated_at}) {
            foreach ($wikidata->claims->{$p_educated_at} as $college)
            {
                $q_educated_at = $college->mainsnak->datavalue->value->id;
                $entities[$q_educated_at] = 'education';
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
            if (!@$data[$entities[$entId]])
                $data[$entities[$entId]] = '';
            $data[$entities[$entId]] .= ($entVal->labels->{$lang}->value. ' ');
        }
        print_r($data); exit();
        return $data;
    }
    public function wikiSection($params)
    {
        $name = $params['name'];
        $section = $params['section'];
        $lang = $params['lang'];

        $url = 'https://'.$lang.'.wikipedia.org/w/api.php?';
        $payload = array (
            'action' => 'parse',
            'format' => 'json',
            'prop' => 'sections',
            'page' => str_replace(' ', '%20', $name)
        );
        $data = $this->api($url, $payload);
        $wikiblock = null;
        foreach ($data->parse->sections as $each)
        {
            if ($each->line == $section)
            {
                $payload['prop'] = 'text';
                $payload['section'] = $each->index;
                $wikiblock = $this->api($url, $payload);
                break;
            }
        }
        $text = $wikiblock->parse->text->{'*'};
        $text = str_replace('\n', '', $text);
        $text = str_replace('\"', '"', $text);
        return $text;
    }
    public function bing($params)
    {
        $name = $params['name'];
        $url = 'https://api.bing.microsoft.com/v7.0/news/search?';
        $payload = array (
            'q' => str_replace(' ',  '%20', $name),
            'count' => 20,
            'sortBy' => 'Date',
            'since' => date_timestamp_get(date_create('2010-12-01'))
        );
        $apikey = '165205352171421bbaecc8e9dc49cc7d';
        $header = array(
            'Ocp-Apim-Subscription-Key:'.$apikey
        );
        $res = $this->api($url, $payload, $header);
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
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }
}
