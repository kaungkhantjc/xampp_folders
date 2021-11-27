<?php

class DirectoryType
{
    public static array $ignoredHomeFiles = array(".", "..", "dashboard", "img", "webalizer", "xampp", "applications.php", "bitnami.css", "favicon.ico", "index.php");
    private static array $ignoredDirectories = array(".", "..");

    public static string $FILE_LARAVEL = "artisan";
    public static string $FILE_ANGULAR = "angular.json";

    public static string $TYPE_ANGULAR = "Angular";
    public static string $TYPE_LARAVEL = "Laravel";
    public static string $TYPE_NORMAL = "Normal";

    public static function isValid(bool $isHome, $file): bool
    {
        return $isHome ? !in_array($file, self::$ignoredHomeFiles) : !in_array($file, self::$ignoredDirectories);
    }
}

class FileUtils
{
    public static function getType($filePath): string
    {
        $pathInfo = pathinfo($filePath);
        return array_key_exists("extension", $pathInfo) ? $pathInfo['extension'] : "unknown";
    }
}

class DirectoryUtils
{
    private static function fileExists($dirPath, $file): bool
    {
        return file_exists($dirPath . "/" . $file);
    }

    public static function getType($dirPath): string
    {
        if (self::fileExists($dirPath, DirectoryType::$FILE_ANGULAR)) {
            return DirectoryType::$TYPE_ANGULAR;
        } else if (self::fileExists($dirPath, DirectoryType::$FILE_LARAVEL)) {
            return DirectoryType::$TYPE_LARAVEL;
        } else {
            return DirectoryType::$TYPE_NORMAL;
        }
    }

    public static function getIcon($type): string
    {
        switch ($type) {
            case DirectoryType::$TYPE_ANGULAR:
                return "angular.svg";

            case DirectoryType::$TYPE_LARAVEL:
                return "laravel.svg";
        }
        return "folder.svg";
    }

    public static function getPath($type, $dirPath): string
    {
        switch ($type) {
            case DirectoryType::$TYPE_LARAVEL:
                return $dirPath . "/public";
        }
        return "applications.php?dir=" . urldecode($dirPath);
    }
}

class AppFile
{
    public bool $isFile;
    public string $name;
    public string $path;
    public ?string $icon;
    public string $type;

    /**
     * AppFile constructor.
     * @param bool $isFile
     * @param string $name
     * @param string $path
     * @param ?string $icon
     * @param string $type
     */
    public function __construct(bool $isFile, string $name, string $path, ?string $icon, string $type = "unknown")
    {
        $this->isFile = $isFile;
        $this->name = $name;
        $this->path = $path;
        $this->icon = $icon;
        $this->type = $type;
    }
}

$appFileList = array();
$homeDirectory = ".";

$currentDir = isset($_GET['dir']) ? urldecode($_GET['dir']) : $homeDirectory;
$isHome = $currentDir === $homeDirectory;

if (is_dir($currentDir)) {
    $scannedList = scandir($currentDir, SCANDIR_SORT_ASCENDING);
    if ($scannedList !== FALSE) {
        foreach ($scannedList as $directory) {
            $fullPath = $currentDir . "/" . $directory;
            if (DirectoryType::isValid($isHome, $directory)) {
                $isFile = is_file($fullPath);
                $type = $isFile ? FileUtils::getType($fullPath) : DirectoryUtils::getType($fullPath);
                $icon = $isFile ? NULL : DirectoryUtils::getIcon($type);
                $filePath = $isFile ? $fullPath : DirectoryUtils::getPath($type, $fullPath);
                $appFile = new AppFile($isFile, $directory, $filePath, $icon, $type);
                array_push($appFileList, $appFile);
            }
        }
    }
}

usort($appFileList, function ($firstFile, $secondFile) {
    if (!$firstFile->isFile && !$secondFile->isFile) {
        return strcasecmp($firstFile->name, $secondFile->name);
    } else {
        return $firstFile->isFile - $secondFile->isFile;
    }
});

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bitnami: Open Source. Simplified</title>
    <link href="bitnami.css" media="all" rel="Stylesheet" type="text/css"/>
    <!--suppress HtmlUnknownTarget -->
    <link href="./dashboard/stylesheets/all.css" rel="stylesheet" type="text/css"/>
    <link href="./dashboard/stylesheets/file-icons.min.css" rel="stylesheet">
</head>
<body>
<div class="contain-to-grid">
    <!--suppress HtmlUnknownTag -->
    <nav class="top-bar" data-topbar>
        <ul class="title-area">
            <li class="name">
                <h1><!--suppress HtmlUnknownTarget -->
                    <a href="./dashboard/index.html">Apache Friends</a></h1>
            </li>
            <li class="toggle-topbar menu-icon">
                <a href="#">
                    <span>Menu</span>
                </a>
            </li>
        </ul>

        <!--suppress HtmlUnknownTag -->
        <section class="top-bar-section">
            <!-- Right Nav Section -->
            <ul class="right">
                <li class="active"><a href="./applications.php">Applications</a></li>
                <li class=""><!--suppress HtmlUnknownTarget -->
                    <a href="./dashboard/faq.html">FAQs</a></li>
                <li class=""><!--suppress HtmlUnknownTarget --><a href="./dashboard/howto.html">HOW-TO Guides</a></li>
                <li class=""><!--suppress HtmlUnknownTarget --><a target="_blank"
                                                                  href="./dashboard/phpinfo.php">PHPInfo</a></li>
                <li class=""><!--suppress HtmlUnknownTarget --><a href="./phpmyadmin/">phpMyAdmin</a></li>
                <li class=""><!--suppress HtmlUnknownTarget --><a href="./phppgadmin/">phpPostgreAdmin</a></li>
            </ul>
        </section>
    </nav>
</div>
<div id="wrapper">
    <div class="hero">
        <div class="row">
            <div class="large-12 columns">
                <p>Apache Friends and Bitnami are cooperating to make dozens of open source applications available on
                    XAMPP, for free. Bitnami-packaged applications include Wordpress, Drupal, Joomla! and dozens of
                    others and can be deployed with one-click installers. Visit the <a
                            href="https://bitnami.com/xampp?utm_source=bitnami&utm_medium=installer&utm_campaign=XAMPP%2BModule"
                            target="_blank">Bitnami XAMPP page</a> for details on the currently available apps.<br/>
                    Check out our <a href="https://www.apachefriends.org/bitnami_for_xampp.html" target="_blank">Bitnami
                        for XAMPP Start Guide</a> for more information about the applications installed.</p>
                <div class="git-layout">
                    <span>If you love it, please star the repository at Github.</span>
                    <!--suppress HtmlUnknownAttribute -->
                    <a class="github-button" href="https://github.com/kaungkhantjc/xampp_folders"
                       data-icon="octicon-star"
                       data-size="large"
                       aria-label="Star kaungkhantjc/xampp_folders on GitHub">Star</a>
                </div>
            </div>
        </div>
    </div>
    <div id="lowerContainer" class="row">
        <div id="content" class="large-12 columns">

            <?php if (!$isHome): ?>
                <div class="home-item">
                    <a href="applications.php">
                        <img src="./dashboard/images/apps/home.svg" alt="home">
                        <span>HOME</span>
                    </a>
                </div>
                <div class="sub-dir-item">
                    <a href="<?php echo "applications.php?dir=" . dirname($currentDir); ?>">
                        <img src="./dashboard/images/apps/subdirectory.svg" alt="parent directory">
                        <span>Parent directory</span>
                    </a>
                </div>
            <?php endif; ?>

            <?php foreach ($appFileList as $appFile): ?>
                <?php if ($appFile->isFile): ?>
                    <div class="list-item">
                        <a href="<?php echo $appFile->path; ?>">
                            <!--suppress HtmlUnknownAttribute -->
                            <i data-file-name="<?php echo $appFile->name; ?>"></i>
                            <span><?php echo $appFile->name; ?></span>
                        </a>
                    </div>
                <?php else : ?>
                    <div class="list-item">
                        <a href="<?php echo $appFile->path; ?>">
                            <img src="./dashboard/images/apps/<?php echo $appFile->icon; ?>" alt="directory">
                            <span><?php echo $appFile->name; ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if (count($appFileList) == 0): ?>
                <div class="list-item"><h4 style="margin: 20px">No files found.</h4></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!--suppress HtmlUnknownTag -->
<footer>
    <div class="row">
        <div class="large-12 columns">
            <div class="row">
                <div class="large-8 columns">
                    <ul class="social">
                        <li class="twitter"><a href="https://twitter.com/apachefriends">Follow us on Twitter</a></li>
                        <li class="facebook"><a href="https://www.facebook.com/we.are.xampp">Like us on Facebook</a>
                        </li>
                        <li class="google"><a href="https://plus.google.com/+xampp/posts">Add us to your G+ Circles</a>
                        </li>
                    </ul>

                    <ul class="inline-list">
                        <li><a href="https://www.apachefriends.org/blog.html">Blog</a></li>
                        <li><a href="https://www.apachefriends.org/privacy_policy.html">Privacy Policy</a></li>
                        <li>
                            <a target="_blank" href="https://www.fastly.com/"> CDN provided by
                                <!--suppress HtmlUnknownAttribute, HtmlUnknownTarget -->
                                <img alt="logo" width="48" data-2x="/dashboard/images/fastly-logo@2x.png"
                                     src="./dashboard/images/fastly-logo.png"/>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="large-4 columns">
                    <p class="text-right">Copyright (c) 2015, Apache Friends</p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- https://github.com/exuanbo/file-icons-js -->
<script src="./dashboard/javascripts/file-icons.min.js" type="text/javascript"></script>
<!-- https://github.com/ntkme/github-buttons -->
<script type="text/javascript" src="./dashboard/javascripts/buttons.js"></script>
<!--suppress NpmUsedModulesInstalled -->
<script type="text/javascript">
    const icons = require('file-icons-js')

    function loadIcon(iconElement, fileName) {
        icons.getClass(fileName).then(function (iconClass) {
            // bug fix for text file in https://github.com/exuanbo/file-icons-js
            iconClass = iconClass.replace("icon-file-text", "text-icon")
            iconElement.classList.value = iconClass
        })
    }

    document.addEventListener("DOMContentLoaded", function () {
        const iconElements = document.querySelectorAll("div.list-item a i")
        iconElements.forEach(function (iconElement) {
            // execute loadIcon method after 50 milliseconds for smooth page loading
            setTimeout(function () {
                loadIcon(iconElement, iconElement.dataset.fileName)
            }, 50)
        })
    })
</script>
</body>
</html>
