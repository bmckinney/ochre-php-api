<?php

namespace App\Http\Controllers;

use App\Helpers\GuzzleHelper;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JsonPath\JsonObject;

class XmlConverter extends Controller
{
    use ApiResponser;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function processXML(Request $request){

        if($request->has('file')){
            $getXML = $request->file;
        }else{

            if($request->has('uuid')){
                $urlGuzzle =   "http://ochre.lib.uchicago.edu/ochre?uuid=" . $request->uuid;
                $getXML = GuzzleHelper::get($urlGuzzle,100);
            }else{
                return null;
            }
        }

        try{
            if($getXML){

                $new_xml = $getXML;
                $new_xml = str_replace(PHP_EOL, '', $new_xml);
                $new_xml = str_replace(array("\n", "\r", "\t"), '', $new_xml);
                $new_xml = trim(str_replace('"', "'", $new_xml));

                $pattern = '/<ochre.+<\/ochre>/';
                preg_match($pattern, $new_xml,$matches);

                try {
                    $xml = simplexml_load_string($matches[0],'SimpleXMLElement');
                } catch (Exception $e) {
                    return $this->successResponse($e->getMessage());
                }

//                $ns = $xml->getDocNamespaces(true);
//                foreach ( $ns as $prefix => $URI )   {
//                    $xml->registerXPathNamespace($prefix, $URI);
//                }

                if($xml){
                    $json = json_encode($xml, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    $obj = new JsonObject($json);
                    // do a bunch of json path stuff here...
                    // $project = $obj->{'$.text.project'};
                    $sections = $obj->{'$.text.discourseHierarchy.section'};
                    return $this->successResponse($sections);
                }else{
                    return $this->errorResponse("XML to JSON Error", Response::HTTP_INTERNAL_SERVER_ERROR);
                }

            }else{
                return $this->errorResponse("URL Error :" . $urlGuzzle, Response::HTTP_BAD_REQUEST);
            }

        }catch(Exception $e){
            return $this->errorResponse("XML Error", Response::HTTP_INTERNAL_SERVER_ERROR);

        }

    }

}
