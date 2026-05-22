<?php

use App\Models\User;
use App\Services\CloudFrontService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

if (! function_exists('fixLanguage')) {
    function fixLanguage($lang)
    {
        $locals = \LaravelLocalization::getSupportedLocales();
        if (isset($locals[$lang])) {
            return $locals[$lang]['name'];
        } else {
            return 'No language';
        }
    }
}

if (! function_exists('giveMeFileFormat')) {
    function giveMeFileFormat($fileFormat)
    {
        $formats = [
            'application/pdf' => 'PDF',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word 2016',
            'application/msword' => 'Word 2003',
            'text/plain' => 'Text',
            'audio/mpeg' => 'Audio',
            'video/mp4' => 'Video',
            'image/jpeg' => 'Image',
            'image/png' => 'Image',
        ];

        if (isset($formats[$fileFormat]) && $formats[$fileFormat] != null) {
            return $formats[$fileFormat];
        } else {
            return '-';
        }
    }
}

if (! function_exists('giveMeResourceIcon')) {
    function giveMeResourceIcon($fileFormat)
    {
        $formats = [
            'application/pdf' => 'fas fa-file-pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'far fa-file-word',
            'application/msword' => 'far fa-file-word',
            'text/plain' => 'fas fa-file-alt',
            'audio/mpeg' => 'fas fa-file-audio',
            'video/mp4' => 'fas fa-video',
        ];

        if ($formats[$fileFormat] != null) {
            $theIcon = $formats[$fileFormat];

            return $formats[$fileFormat];
        } else {
            return $formats['application/pdf'];
        }
    }
}

// Abstracts in Drupal installation had /sites/default/files/learn-1044078_960_720_0.jpg type image links
// In here, I am fixing that and applying Laravel's way of showing images
if (! function_exists('fixImage')) {
    function fixImage($abstract, $resource_id, $isThumbnail = false, $onlyFileName = false)
    {
        // To replace hardcoded url to dynamic base_url
        $abstract = str_replace('http://www.darakhtdanesh.org/', URL::to('/').'/', $abstract);

        if (env('DDL_LITE') == 'yes') {
            if (strpos($abstract, '<div class="media_embed"') == true || strpos($abstract, '<div class="embeddedContent') == true) {
                $abstract = preg_replace('#<div class="media_embed" height="315px" width="560px">(.*?)</div>#', '', $abstract);
                $abstract = $abstract."
				<video width='560' height='315' controls>
				<source src='".URL::to('/storage/videos/'.$resource_id.'.mp4')."' type='video/mp4'>
				Your browser does not support the video tag.
				</video>
				";
            }
        }

        preg_match_all('/src="([^"]*)"/', $abstract, $matches);
        if ($matches[1]) {
            $replaceMe = [];
            $originalMe = [];

            for ($i = 0; $i < count($matches[1]); $i++) {
                $absStr = $matches[1][$i];
                if (strpos($absStr, 'youtube') == false && strpos($absStr, 'google') == false) {
                    $absArray = explode('/', $absStr);
                    $imageName = last($absArray);
                    if($onlyFileName){
                        $imageName = explode('?', $imageName, 2)[0];
                        $fixedImage = $imageName;
                    }else{
                        if($isThumbnail){
                            $imageName = explode('?', $imageName, 2)[0];
                            $fixedImage = getFile("files/thumbnails/$imageName");
                        }else{
                            if (Storage::disk('public')->exists($imageName)) {
                                $fixedImage = Storage::disk('public')->url($imageName);
                            } else {
                                $fixedImage = '';
                            }
                        }
                    }

                    array_push($replaceMe, $fixedImage);
                    array_push($originalMe, $absStr);
                }
            }
            $finalFixedStr = str_replace($originalMe, $replaceMe, $abstract);

            return $finalFixedStr;
        } else {
            return $abstract;
        }
    }
}

if (! function_exists('getImagefromResource')) {
    function getImagefromResource($abstract)
    {
        preg_match('/src="([^"]*)"/', $abstract, $matches);
        if (count($matches) > 0) {
            $absStr = $matches[1];
            if (strpos($absStr, 'youtube') == false) {
                $absArray = explode('/', $absStr);
                $imageName = last($absArray);
                if (Storage::disk('public')->exists($imageName)) {
                    return Storage::disk('public')->url($imageName);
                }
            }
        }

        return asset('storage/files/placeholder_image.png');
    }
}

if (! function_exists('unpackResourceObject')) {
    function unpackResourceObject($resourceObject, $field)
    {
        $str = '';
        foreach ($resourceObject as $obj) {
            $str .= $obj->$field.', ';
        }
        $str = rtrim($str, ', ');

        return $str;
    }
}

if (! function_exists('checkUserPassword')) {
    function checkUserPassword($planePassword, $userPassword)
    {
        // include(app_path() . '/support/DrupalPasswordHasher.php');

        if (user_check_password($planePassword, $userPassword)) {
            return true;
        } else {
            return false;
        }
    }
}

if (! function_exists('getCountry')) {
    function getCountry($tnid)
    {
        $term = App\Models\TaxonomyTerm::where('tnid', $tnid)->
            where('language', 'en')->
            where('vid', 15)->
            first();

        if ($term != null) {
            return $term->name;
        } else {
            return $tnid;
        }
    }
}
if (! function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        $user = new User;

        if ($user->isAdministrator(Auth::id())) {
            return true;
        } else {
            return false;
        }
    }
}

if (! function_exists('isNormalUser')) {
    function isNormalUser(): bool
    {
        $user = new User;

        if ($user->isNormalUser(Auth::id())) {
            return true;
        } else {
            return false;
        }
    }
}

if (! function_exists('isLibraryManager')) {
    function isLibraryManager(): bool
    {
        $user = new User;

        if ($user->isLibraryManager(Auth::id())) {
            return true;
        } else {
            return false;
        }
    }
}

if (! function_exists('formatBytes')) {
    function formatBytes($size, $precision = 2): string
    {
        $base = log($size, 1024);
        $suffixes = ['', 'KB', 'MB', 'GB', 'TB'];

        return round(pow(1024, $base - floor($base)), $precision).' '.$suffixes[floor($base)];
    }
}

if (! function_exists('encodeUrl')) {
    function encodeUrl($string)
    {
        $entities = ['%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D'];
        $replacements = ['!', '*', "'", '(', ')', ';', ':', '@', '&', '=', '+', '$', ',', '/', '?', '%', '#', '[', ']'];

        return str_replace($entities, $replacements, urlencode($string));
    }
}

/**
 * Parses a user agent string into its important parts
 *
 * @param  string|null  $u_agent  User agent string to parse or null. Uses $_SERVER['HTTP_USER_AGENT'] on NULL
 * @return string[] an array with browser, version and platform keys
 *
 * @throws InvalidArgumentException on not having a proper user agent to parse.
 *
 * @author Jesse G. Donat <donatj@gmail.com>
 *
 * @link https://github.com/donatj/PhpUserAgent
 * @link http://donatstudios.com/PHP-Parser-HTTP_USER_AGENT
 */
function parse_user_agent(?string $u_agent = null): array
{
    if (is_null($u_agent)) {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $u_agent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            throw new InvalidArgumentException('parse_user_agent requires a user agent');
        }
    }

    $platform = null;
    $browser = null;
    $version = null;

    $empty = ['platform' => $platform, 'browser' => $browser, 'version' => $version];

    if (! $u_agent) {
        return $empty;
    }

    if (preg_match('/\((.*?)\)/im', $u_agent, $parent_matches)) {
        preg_match_all('/(?P<platform>BB\d+;|Android|CrOS|Tizen|iPhone|iPad|iPod|Linux|(Open|Net|Free)BSD|Macintosh|Windows(\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|X11|(New\ )?Nintendo\ (WiiU?|3?DS|Switch)|Xbox(\ One)?)
				(?:\ [^;]*)?
				(?:;|$)/imx', $parent_matches[1], $result, PREG_PATTERN_ORDER);

        $priority = ['Xbox One', 'Xbox', 'Windows Phone', 'Tizen', 'Android', 'FreeBSD', 'NetBSD', 'OpenBSD', 'CrOS', 'X11'];

        $result['platform'] = array_unique($result['platform']);
        if (count($result['platform']) > 1) {
            if ($keys = array_intersect($priority, $result['platform'])) {
                $platform = reset($keys);
            } else {
                $platform = $result['platform'][0];
            }
        } elseif (isset($result['platform'][0])) {
            $platform = $result['platform'][0];
        }
    }

    if ($platform == 'linux-gnu' || $platform == 'X11') {
        $platform = 'Linux';
    } elseif ($platform == 'CrOS') {
        $platform = 'Chrome OS';
    }

    preg_match_all('%(?P<browser>Camino|Kindle(\ Fire)?|Firefox|Iceweasel|IceCat|Safari|MSIE|Trident|AppleWebKit|
				TizenBrowser|(?:Headless)?Chrome|Vivaldi|IEMobile|Opera|OPR|Silk|Midori|Edge|CriOS|UCBrowser|Puffin|SamsungBrowser|
				Baiduspider|Googlebot|YandexBot|bingbot|Lynx|Version|Wget|curl|
				Valve\ Steam\ Tenfoot|
				NintendoBrowser|PLAYSTATION\ (\d|Vita)+)
				\)?;?
				(?:[:/ ](?P<version>[0-9A-Z.]+)|/[A-Z]*)%ix',
        $u_agent, $result, PREG_PATTERN_ORDER);

    // If nothing matched, return null (to avoid undefined index errors)
    if (! isset($result['browser'][0]) || ! isset($result['version'][0])) {
        if (preg_match('%^(?!Mozilla)(?P<browser>[A-Z0-9\-]+)(/(?P<version>[0-9A-Z.]+))?%ix', $u_agent, $result)) {
            return ['platform' => $platform ?: null, 'browser' => $result['browser'], 'version' => isset($result['version']) ? $result['version'] ?: null : null];
        }

        return $empty;
    }

    if (preg_match('/rv:(?P<version>[0-9A-Z.]+)/si', $u_agent, $rv_result)) {
        $rv_result = $rv_result['version'];
    }

    $browser = $result['browser'][0];
    $version = $result['version'][0];

    $lowerBrowser = array_map('strtolower', $result['browser']);

    $find = function ($search, &$key, &$value = null) use ($lowerBrowser) {
        $search = (array) $search;

        foreach ($search as $val) {
            $xkey = array_search(strtolower($val), $lowerBrowser);
            if ($xkey !== false) {
                $value = $val;
                $key = $xkey;

                return true;
            }
        }

        return false;
    };

    $key = 0;
    $val = '';
    if ($browser == 'Iceweasel' || strtolower($browser) == 'icecat') {
        $browser = 'Firefox';
    } elseif ($find('Playstation Vita', $key)) {
        $platform = 'PlayStation Vita';
        $browser = 'Browser';
    } elseif ($find(['Kindle Fire', 'Silk'], $key, $val)) {
        $browser = $val == 'Silk' ? 'Silk' : 'Kindle';
        $platform = 'Kindle Fire';
        if (! ($version = $result['version'][$key]) || ! is_numeric($version[0])) {
            $version = $result['version'][array_search('Version', $result['browser'])];
        }
    } elseif ($find('NintendoBrowser', $key) || $platform == 'Nintendo 3DS') {
        $browser = 'NintendoBrowser';
        $version = $result['version'][$key];
    } elseif ($find('Kindle', $key, $platform)) {
        $browser = $result['browser'][$key];
        $version = $result['version'][$key];
    } elseif ($find('OPR', $key)) {
        $browser = 'Opera Next';
        $version = $result['version'][$key];
    } elseif ($find('Opera', $key, $browser)) {
        $find('Version', $key);
        $version = $result['version'][$key];
    } elseif ($find('Puffin', $key, $browser)) {
        $version = $result['version'][$key];
        if (strlen($version) > 3) {
            $part = substr($version, -2);
            if (ctype_upper($part)) {
                $version = substr($version, 0, -2);

                $flags = ['IP' => 'iPhone', 'IT' => 'iPad', 'AP' => 'Android', 'AT' => 'Android', 'WP' => 'Windows Phone', 'WT' => 'Windows'];
                if (isset($flags[$part])) {
                    $platform = $flags[$part];
                }
            }
        }
    } elseif ($find(['IEMobile', 'Edge', 'Midori', 'Vivaldi', 'SamsungBrowser', 'Valve Steam Tenfoot', 'Chrome', 'HeadlessChrome'], $key, $browser)) {
        $version = $result['version'][$key];
    } elseif ($rv_result && $find('Trident', $key)) {
        $browser = 'MSIE';
        $version = $rv_result;
    } elseif ($find('UCBrowser', $key)) {
        $browser = 'UC Browser';
        $version = $result['version'][$key];
    } elseif ($find('CriOS', $key)) {
        $browser = 'Chrome';
        $version = $result['version'][$key];
    } elseif ($browser == 'AppleWebKit') {
        if ($platform == 'Android') {
            // $key = 0;
            $browser = 'Android Browser';
        } elseif (strpos($platform, 'BB') === 0) {
            $browser = 'BlackBerry Browser';
            $platform = 'BlackBerry';
        } elseif ($platform == 'BlackBerry' || $platform == 'PlayBook') {
            $browser = 'BlackBerry Browser';
        } else {
            $find('Safari', $key, $browser) || $find('TizenBrowser', $key, $browser);
        }

        $find('Version', $key);
        $version = $result['version'][$key];
    } elseif ($pKey = preg_grep('/playstation \d/i', array_map('strtolower', $result['browser']))) {
        $pKey = reset($pKey);

        $platform = 'PlayStation '.preg_replace('/[^\d]/i', '', $pKey);
        $browser = 'NetFront';
    }

    return ['platform' => $platform ?: null, 'browser' => $browser ?: null, 'version' => $version ?: null];
}

if (! function_exists('DDLClearSession')) {
    function DDLClearSession(): void
    {
        // setting the search session empty
        if (
            session()->has('resource1') ||
            session()->has('resource2') ||
            session()->has('resource3') ||
            session()->has('search')
        ) {
            session()->forget(['resource1', 'resource2', 'resource3', 'search']);
        }
    }
}

if (! function_exists('en')) {
    function en($phrase = '')
    {
        return (Lang::locale() != 'en') ? '('.Lang::get($phrase).')' : '';
    }
}

if (! function_exists('termEn')) {
    function termEn($id = ''): string
    {
        $tnid = App\Models\TaxonomyTerm::where('id', $id)->first()->tnid;

        $term = App\Models\TaxonomyTerm::where('tnid', $tnid)->where('language', 'en')->first();

        return ($term != null) ? ' ('.$term->name.')' : '';
    }
}

if (! function_exists('get_pdf_version')) {
    function get_pdf_version_and_pages($file): float|int
    {
        $process = new Process(['pdfinfo', $file]);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        // Split the string $output at every instance
        // of a newline character (/n) or PHP_EOL
        $output = explode(PHP_EOL, $output);

        $version = 0;
        foreach ($output as $each_line) {
            // Regex to match "PDF version:   #.#"
            if (preg_match('/PDF version:\s*(\d.\d)/i', $each_line, $matches) === 1) {
                $version = floatval($matches[1]);
            }
        }

        return $version;
    }
}

if (! function_exists('lower_pdf_version')) {
    function lower_pdf_version($old_file, $file_name): string
    {
        $new_file = tempnam(sys_get_temp_dir(), $file_name[0].'_');
        rename($new_file, $new_file .= '.pdf');
        $process = new Process([
            'gs',  // Invokes Ghostscript
            '-sDEVICE=pdfwrite',  // sets PDF as output device
            '-dCompatibilityLevel=1.4',  // we do the actual conversion here
            '-dNOPAUSE',  // no interaction
            '-dBATCH',  // no interaction
            '-sOutputFile='.$new_file,
            $old_file,
        ]);
        $process->run();
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $new_file;
    }
}

if (! function_exists('get_license_buttons')) {
    function get_license_buttons($resource): array
    {
        $license_button_1 = null;
        $license_button_2 = null;

        if ($resource->creativeCommons) {
            $license = $resource->creativeCommons[0]->name;
            /*
            We can add a license as long as it is a CC license
            and is formatted certain way. The formats we currently
            support are string values picked up from the database, and
            form the conditional statements below.
            */
            try {
                if ($license === 'CC 0 / public domain') {
                    $license_button_1 = Storage::disk('s3')
                        ->get('public/img/cc-zero.png');
                } elseif ($license === 'CC BY / CC BY-SA' or $license === 'CC BY 4.0') {
                    if ($license === 'CC BY / CC BY-SA') {
                        $license_button_1 = Storage::disk('s3')
                            ->get('public/img/by-sa.png');
                    }
                    $license_button_2 = Storage::disk('s3')
                        ->get('public/img/by.png');
                } elseif ($license === 'CC BY-NC / CC BY-NC-SA') {
                    $license_button_1 = Storage::disk('s3')
                        ->get('public/img/by-nc.png');
                    $license_button_2 = Storage::disk('s3')
                        ->get('public/img/by-nc-sa.png');
                } elseif ($license === 'CC BY-ND / CC BY-NC-ND') {
                    $license_button_1 = Storage::disk('s3')
                        ->get('public/img/by-nd.png');
                    $license_button_2 = Storage::disk('s3')
                        ->get('public/img/by-nc-nd.png');
                }
            } catch (FileNotFoundException $e) {

            }
        }
        if ($license_button_1) {
            $temp_license_button_1 = tempnam(
                sys_get_temp_dir(),
                'license_button_1_');
            file_put_contents($temp_license_button_1, $license_button_1);
            $license_button_1 = $temp_license_button_1;
        }
        if ($license_button_2) {
            $temp_license_button_2 = tempnam(
                sys_get_temp_dir(),
                'license_button_2_');
            file_put_contents($temp_license_button_2, $license_button_2);
            $license_button_2 = $temp_license_button_2;
        }

        return [$license_button_1, $license_button_2];
    }
}

if (! function_exists('watermark_pdf')) {
    function watermark_pdf($file, $logo, $license_button_1, $license_button_2)
    {
        $pdf = new FPDI;
        try {
            $pages = $pdf->setSourceFile($file);
        } catch (PdfParserException) {
            return $file;
        }
        for ($i = 1; $i <= $pages; $i++) {
            try {
                $tpl = $pdf->importPage($i);
            } catch (
                CrossReferenceException|
                FilterException|
                PdfParserException|
                PdfTypeException|
                PdfReaderException
            ) {
                return $file;
            }

            $pdf->addPage();
            [$page_width, $page_height] = $pdf->getTemplateSize($tpl);
            $pdf->useTemplate(
                $tpl,
                1,  // start drawing the page at the beginning of x-axis
                1,  // start drawing the page at the beginning of x-axis
                $page_width,  // set width from the imported page
                null,  // null, to maintain the aspect ratio
                true  // resize page to fit the imported page
            );
            if ($i <= 20) {
                $x_pos = $page_width - 55;
                $y_pos = (
                    $page_height - (
                        (
                            $i == 1 and
                            ($license_button_1 or $license_button_2)
                        ) ? 25 : 17  // 25, if we're in the first iteration
                    )                // 17, if we're not and there's no license
                );

                $pdf->Image(
                    $logo,
                    $x_pos,
                    $y_pos,
                    50,  // watermark logo width
                    0,  // height = 0, to maintain aspect ratio
                    'png'
                );
                if ($i == 1 and ($license_button_1 or $license_button_2)) {
                    if ($license_button_1) {
                        $pdf->Image(
                            $license_button_1,
                            // if we have another button to place, make room
                            $page_width - 25 + (
                                $license_button_2 ? -25 : 0
                            ),
                            $page_height - 10,
                            20,  // license button width
                            0,  // height = 0, to maintain aspect ratio
                            'png'
                        );
                    }
                    if ($license_button_2) {
                        $pdf->Image(
                            $license_button_2,
                            $page_width - 25,
                            $page_height - 10,
                            20,  // license button width
                            0,  // height = 0, to maintain aspect ratio
                            'png'
                        );
                    }
                }
            }
        }

        return $pdf->Output('S');  // S: return the document as a string.
    }

    if (! function_exists('getResourceImage')) {
        function getResourceImage($image, $isThumbnail = false)
        {
            if ($isThumbnail) {
                $thumbnailPath = "files/thumbnails/$image";

                return getFile($thumbnailPath);
            }

            return getFile("files/$image");
        }
    }

    if (! function_exists('getFile')) {
        function getFile($file): string
        {
            if (config('app.env') != 'production') {
                return Storage::disk('public')->url($file);
            } else {
                return app(CloudFrontService::class)->signedUrl($file);
            }
        }
    }

}
