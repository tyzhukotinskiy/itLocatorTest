<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Landing extends Model
{

    use HasFactory;

    protected $fillable = [
        'first_header', 'second_header', 'user_id', 'image', 'template', 'font_color', 'domen', 'content'
    ];

    public static function getLandingRoot($domain)
    {
        $handle = @fopen("/etc/nginx/sites-enabled/".$domain, "r");
        $root = '';

        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                if (preg_match('/^root/', trim($buffer)))
                    $root = str_replace(';', '', explode(' ',trim($buffer))[1]);
            }
            fclose($handle);
        }

        return $root;
    }

    public static function setLandingIndex ($root, $data)
    {

        $indexFileCopy = $root . 'index_copy.html';
        $indexFile = $root . 'index.html';

        copy($indexFileCopy, $indexFile);

        $indexFileText = file_get_contents($indexFile);

        $indexFileText = str_replace('@first_header', $data['first_header'], $indexFileText);
        $indexFileText = str_replace('@second_header', $data['second_header'], $indexFileText);
        $indexFileText = str_replace('@content', $data['content'], $indexFileText);
        if (array_key_exists('image', $data)) {
            $indexFileText = str_replace('@image', $data['image'], $indexFileText);
        }

        file_put_contents($indexFile, $indexFileText);
    }

    public static function setLandingStyles ($root, $data)
    {
        $styleFileCopy = $root . 'css/main_copy.css';
        $styleFile = $root . 'css/main.css';

        copy($styleFileCopy, $styleFile);

        $styleFileText = file_get_contents($styleFile);

        $styleFileText = str_replace('@font_color', $data['font_color'], $styleFileText);

        file_put_contents($styleFile, $styleFileText);
    }

    public static function addLanding($data)
    {
        $root = Landing::getLandingRoot($data['domen']);

        $from = base_path().'/resources/views/landingLayouts/'.$data['template'];
        $landingDir = $root.'/';
        self::moveDirectory($from, $landingDir);
        $indexData = [
            'first_header' => $data['first_header'],
            'second_header' => $data['second_header'],
            'content' => $data['content'],
        ];
        $styleData = [
            'font_color' => $data['font_color']
        ];

        Landing::setLandingIndex($landingDir, $indexData);
        Landing::setLandingStyles($landingDir, $styleData);
    }

    public static function moveDirectory($from, $new_dir)
    {
        if (is_dir($from)) {
            @mkdir($new_dir);
            $d = dir($from);
            while (false !== ($entry = $d->read())) {
                if ($entry == "." || $entry == "..") continue;
                self::moveDirectory("$from/$entry", "$new_dir/$entry");
            }
            $d->close();
        } else {
            copy($from, $new_dir);
        }

    }

    public static function addLandingImage($files, $projectDir, $landing)
    {
        $filename = $files['file']['name'];

        $location = $projectDir."/images/".$filename;
        $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
        $imageFileType = strtolower($imageFileType);

        $valid_extensions = array("jpg","jpeg","png");

        if(in_array(strtolower($imageFileType), $valid_extensions)) {
            /* Upload file */
            if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
                $response = $location;
                $landing->image = $filename;
                $landing->save();
                $indexData = [
                    'first_header' => $landing->first_header,
                    'second_header' => $landing->second_header,
                    'content' => $landing->content,
                    'image' => $filename
                ];
                Landing::setLandingIndex($projectDir.'/', $indexData);
            }
        }
    }

}
