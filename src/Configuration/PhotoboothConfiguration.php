<?php

namespace Photobooth\Configuration;

use Photobooth\Enum\ImageFilterEnum;
use Photobooth\Environment;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class PhotoboothConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('photobooth');

        $rootNode = $treeBuilder->getRootNode()->addDefaultsIfNotSet();
        $rootNode
            // we are ignoring extra keys to avoid having old configuration mixed up
            // only the current configuration will be processed
            ->ignoreExtraKeys()
            ->children()
                ->append($this->addUiNode())
                ->append($this->addAdminPanel())
                ->append($this->addDev())
                ->append($this->addWebserver())
                ->append($this->addStartScreen())
                ->append($this->addLogo())
                ->append($this->addDownload())
                ->append($this->addReload())
                ->append($this->addPicture())
                ->append($this->addTextOnPicture())
                ->append($this->addDatabase())
                ->append($this->addDelete())
                ->append($this->addEvent())
                ->append($this->addButton())
                ->append($this->addFilters())
                ->append($this->addCustom())
                ->append($this->addCollage())
                ->append($this->addTextOnCollage())
                ->append($this->addQuality())
                ->append($this->addLogin())
                ->append($this->addFtp())
                ->append($this->addPhotoSwipe())
                ->append($this->addVideo())
                ->append($this->addGallery())
                ->append($this->addGetRequests())
                ->append($this->addProtect())
                ->append($this->addColors())
                ->append($this->addBackground())
                ->append($this->addPreview())
                ->append($this->addIcons())
                ->append($this->addKeying())
                ->append($this->addSyncToDrive())
                ->append($this->addRemoteBuzzer())
                ->append($this->addSlideshow())
                ->append($this->addTextOnPrint())
                ->append($this->addQr())
                ->append($this->addChromaCapture())
                ->append($this->addPrint())
                ->append($this->addCommands())
                ->append($this->addMail())
                ->append($this->addSound())
            ->end();

        return $treeBuilder;
    }

    protected function addSound(): NodeDefinition
    {
        return (new TreeBuilder('sound'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(false)->end()
                ->booleanNode('countdown_enabled')->defaultValue(true)->end()
                ->booleanNode('cheese_enabled')->defaultValue(true)->end()
                ->booleanNode('fallback_enabled')->defaultValue(true)->end()
                ->enumNode('voice')
                    ->values([
                        'woman',
                        'man',
                        'custom',
                    ])
                    ->defaultValue('man')
                    ->end()
            ->end();
    }

    protected function addMail(): NodeDefinition
    {
        return (new TreeBuilder('mail'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(false)->end()
                ->booleanNode('send_all_later')->defaultValue(false)->end()
                ->scalarNode('subject')->defaultValue('')->end()
                ->scalarNode('text')->defaultValue('')->end()
                ->scalarNode('alt_text')->defaultValue('')->end()
                ->booleanNode('is_html')->defaultValue(false)->end()
                ->scalarNode('host')->defaultValue('smtp.example.com')->end()
                ->scalarNode('username')->defaultValue('photobooth@example.com')->end()
                ->scalarNode('password')->defaultValue('yourpassword')->end()
                ->scalarNode('fromAddress')->defaultValue('photobooth@example.com')->end()
                ->scalarNode('fromName')->defaultValue('Photobooth')->end()
                ->scalarNode('file')->defaultValue('mail-adresses')->end()
                ->scalarNode('secure')->defaultValue('tls')->end()
                ->integerNode('port')
                    ->defaultValue(587)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
            ->end();
    }

    protected function addCommands(): NodeDefinition
    {
        $commands = [
            'windows' => [
                'take_picture' => 'digicamcontrol\CameraControlCmd.exe /capture /filename %s',
                'take_video' => '',
                'take_custom' => '',
                'print' => 'rundll32 C:\WINDOWS\system32\shimgvw.dll,ImageView_PrintTo %s Printer_Name',
                'exiftool' => '',
                'nodebin' => '',
                'reboot' => '',
                'shutdown' => '',
                'preview' => '',
                'preview_kill' => '',
                'pre_photo' => '',
                'post_photo' => '',
            ],
            'linux' => [
                'take_picture' => 'gphoto2 --capture-image-and-download --filename=%s',
                'take_video' => 'python3 cameracontrol.py -v %s --vlen 3 --vframes 4',
                'take_custom' => 'python3 cameracontrol.py --chromaImage=/var/www/html/resources/img/bg.jpg --chromaColor 00ff00 --chromaSensitivity 0.4 --chromaBlend 0.1 --capture-image-and-download %s',
                'print' => 'lp -o landscape -o fit-to-page %s',
                'exiftool' => 'exiftool -overwrite_original -TagsFromFile %s %s',
                'nodebin' => '/usr/bin/node',
                'reboot' => '/sbin/shutdown -r now',
                'shutdown' => '/sbin/shutdown -h now',
                'preview' => '',
                'preview_kill' => '',
                'pre_photo' => '',
                'post_photo' => '',
            ],
        ];
        $commandDefaults = $commands[Environment::getOperatingSystem()];

        return (new TreeBuilder('commands'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('take_picture')->defaultValue($commandDefaults['take_picture'])->end()
                ->scalarNode('take_custom')->defaultValue($commandDefaults['take_custom'])->end()
                ->scalarNode('take_video')->defaultValue($commandDefaults['take_video'])->end()
                ->scalarNode('print')->defaultValue($commandDefaults['print'])->end()
                ->scalarNode('exiftool')->defaultValue($commandDefaults['exiftool'])->end()
                ->scalarNode('preview')->defaultValue($commandDefaults['preview'])->end()
                ->scalarNode('preview_kill')->defaultValue($commandDefaults['preview_kill'])->end()
                ->scalarNode('nodebin')->defaultValue($commandDefaults['nodebin'])->end()
                ->scalarNode('pre_photo')->defaultValue($commandDefaults['pre_photo'])->end()
                ->scalarNode('post_photo')->defaultValue($commandDefaults['post_photo'])->end()
                ->scalarNode('reboot')->defaultValue($commandDefaults['reboot'])->end()
                ->scalarNode('shutdown')->defaultValue($commandDefaults['shutdown'])->end()
            ->end();
    }

    protected function addPrint(): NodeDefinition
    {
        return (new TreeBuilder('print'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('from_result')->defaultValue(false)->end()
                ->booleanNode('from_gallery')->defaultValue(false)->end()
                ->booleanNode('from_chromakeying')->defaultValue(false)->end()
                ->booleanNode('auto')->defaultValue(false)->end()
                ->integerNode('auto_delay')
                    ->min(250)
                    ->max(10000)
                    ->defaultValue(1000)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('time')
                    ->min(250)
                    ->max(20000)
                    ->defaultValue(5000)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('limit')
                    ->defaultValue(0)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->scalarNode('locking_msg')->defaultValue('Printing... Print limit reached, print will be locked.')->end()
                ->scalarNode('limit_msg')->defaultValue('Print locked.')->end()
                ->booleanNode('no_rotate')->defaultValue(false)->end()
                ->scalarNode('key')->defaultValue('')->end()
                ->booleanNode('qrcode')->defaultValue(false)->end()
                ->integerNode('qrSize')
                    ->defaultValue(4)
                    ->min(4)
                    ->max(10)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->enumNode('qrPosition')
                    ->values(['topLeft', 'top', 'topRight', 'right', 'bottomRight', 'bottom', 'bottomLeft', 'left'])
                    ->defaultValue('bottomRight')
                    ->end()
                ->integerNode('qrOffset')
                    ->defaultValue(10)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('qrMargin')
                    ->defaultValue(4)
                    ->min(0)
                    ->max(10)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->scalarNode('qrBgColor')->defaultValue('#ffffff')->end()
                ->booleanNode('print_frame')->defaultValue(false)->end()
                ->scalarNode('frame')->defaultValue('')->end()
                ->booleanNode('crop')->defaultValue(false)->end()
                ->integerNode('crop_width')
                    ->defaultValue(1000)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('crop_height')
                    ->defaultValue(500)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
            ->end();
    }

    protected function addChromaCapture(): NodeDefinition
    {
        return (new TreeBuilder('chromaCapture'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(false)->end()
            ->end();
    }

    protected function addQr(): NodeDefinition
    {
        return (new TreeBuilder('qr'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(true)->end()
                ->scalarNode('url')->defaultValue('')->end()
                ->booleanNode('append_filename')->defaultValue(true)->end()
                ->booleanNode('custom_text')->defaultValue(false)->end()
                ->scalarNode('text')->defaultValue('')->end()
                ->enumNode('result')
                    ->values([
                        'hidden',
                        'left',
                        'left left--top',
                        'left left--center',
                        'left left--bottom',
                        'right',
                        'right right--top',
                        'right right--center',
                        'right right--bottom',
                    ])
                    ->defaultValue('hidden')
                    ->end()
            ->end();
    }

    protected function addTextOnPrint(): NodeDefinition
    {
        return (new TreeBuilder('textonprint'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(false)->end()
                ->scalarNode('line1')->defaultValue('line 1')->end()
                ->scalarNode('line2')->defaultValue('line 2')->end()
                ->scalarNode('line3')->defaultValue('line 3')->end()
                ->integerNode('locationx')
                    ->defaultValue(2250)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('locationy')
                    ->defaultValue(1050)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('rotation')
                    ->defaultValue(40)
                    ->min(0)
                    ->max(359)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->scalarNode('font')->defaultValue('')->end()
                ->scalarNode('font_color')->defaultValue('#ffffff')->end()
                ->integerNode('font_size')
                    ->defaultValue(100)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('linespace')
                    ->defaultValue(100)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
            ->end();
    }

    protected function addSlideshow(): NodeDefinition
    {
        return (new TreeBuilder('slideshow'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->integerNode('refreshTime')
                    ->defaultValue(60)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('pictureTime')
                    ->defaultValue(3000)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->booleanNode('randomPicture')->defaultValue(true)->end()
                ->booleanNode('use_thumbs')->defaultValue(false)->end()
            ->end();
    }

    protected function addRemoteBuzzer(): NodeDefinition
    {
        // On Raspberry Pi 5 high-value GPIO are used on sysfs
        $highValueGpioSysfs = false;
        $modelFilePath = '/proc/device-tree/model';
        if (file_exists($modelFilePath)) {
            $model = (string) shell_exec('tr -d "\0" < ' . $modelFilePath);
            if (strpos($model, 'Raspberry Pi 5') !== false) {
                $highValueGpioSysfs = true;
            }
        }

        return (new TreeBuilder('remotebuzzer'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('startserver')->defaultValue(false)->end()
                ->scalarNode('serverip')->defaultValue('')->end()
                ->integerNode('port')
                    ->defaultValue(14711)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->booleanNode('usebuttons')->defaultValue(false)->end()
                ->booleanNode('userotary')->defaultValue(false)->end()
                ->booleanNode('enable_standalonegallery')->defaultValue(false)->end()
                ->booleanNode('usenogpio')->defaultValue(false)->end()
                ->booleanNode('picturebutton')->defaultValue(true)->end()
                ->integerNode('collagetime')
                    ->defaultValue(2)
                    ->min(1)
                    ->max(6)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->booleanNode('collagebutton')->defaultValue(false)->end()
                ->booleanNode('printbutton')->defaultValue(false)->end()
                ->booleanNode('shutdownbutton')->defaultValue(false)->end()
                ->integerNode('shutdownholdtime')
                    ->defaultValue(5)
                    ->min(0)
                    ->max(9)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('debounce')
                    ->defaultValue(30)
                    ->min(0)
                    ->max(100)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->booleanNode('rebootbutton')->defaultValue(false)->end()
                ->integerNode('rebootholdtime')
                    ->defaultValue(5)
                    ->min(0)
                    ->max(9)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->booleanNode('useleds')->defaultValue(false)->end()
                ->booleanNode('photolight')->defaultValue(false)->end()
                ->booleanNode('pictureled')->defaultValue(false)->end()
                ->booleanNode('collageled')->defaultValue(false)->end()
                ->booleanNode('shutdownled')->defaultValue(false)->end()
                ->booleanNode('rebootled')->defaultValue(false)->end()
                ->booleanNode('printled')->defaultValue(false)->end()
                ->booleanNode('videobutton')->defaultValue(false)->end()
                ->booleanNode('videoled')->defaultValue(false)->end()
                ->booleanNode('custombutton')->defaultValue(false)->end()
                ->booleanNode('customled')->defaultValue(false)->end()
                ->enumNode('move2usb')
                    ->values(['disabled', 'copy', 'move'])
                    ->defaultValue('disabled')
                    ->end()
                ->booleanNode('move2usbled')->defaultValue(false)->end()
                // GPIO
                ->integerNode('rotaryclkgpio')
                    ->defaultValue($highValueGpioSysfs ? 426 : 27)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('rotarydtgpio')
                    ->defaultValue($highValueGpioSysfs ? 416 : 17)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('rotarybtngpio')
                    ->defaultValue($highValueGpioSysfs ? 421 : 22)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('picturegpio')
                    ->defaultValue($highValueGpioSysfs ? 420 : 21)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('collagegpio')
                    ->defaultValue($highValueGpioSysfs ? 419 : 20)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('printgpio')
                    ->defaultValue($highValueGpioSysfs ? 425 : 26)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('shutdowngpio')
                    ->defaultValue($highValueGpioSysfs ? 415 : 16)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('rebootgpio')
                    ->defaultValue($highValueGpioSysfs ? 407 : 8)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('photolightgpio')
                    ->defaultValue($highValueGpioSysfs ? 424 : 25)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('pictureledgpio')
                    ->defaultValue($highValueGpioSysfs ? 418 : 19)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('collageledgpio')
                    ->defaultValue($highValueGpioSysfs ? 411 : 12)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('shutdownledgpio')
                    ->defaultValue($highValueGpioSysfs ? 422 : 23)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('rebootledgpio')
                    ->defaultValue($highValueGpioSysfs ? 417 : 18)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('printledgpio')
                    ->defaultValue($highValueGpioSysfs ? 409 : 10)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('videogpio')
                    ->defaultValue($highValueGpioSysfs ? 406 : 7)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('videoledgpio')
                    ->defaultValue($highValueGpioSysfs ? 408 : 9)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('customgpio')
                    ->defaultValue($highValueGpioSysfs ? 404 : 5)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('customledgpio')
                    ->defaultValue($highValueGpioSysfs ? 423 : 24)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('move2usbgpio')
                    ->defaultValue($highValueGpioSysfs ? 405 : 6)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('move2usbledgpio')
                    ->defaultValue($highValueGpioSysfs ? 410 : 11)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
            ->end();
    }

    protected function addSyncToDrive(): NodeDefinition
    {
        return (new TreeBuilder('synctodrive'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(false)->end()
                ->scalarNode('target')->defaultValue('photobooth')->end()
                ->integerNode('interval')
                    ->defaultValue(300)
                    ->min(10)
                    ->max(600)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
            ->end();
    }

    protected function addKeying(): NodeDefinition
    {
        return (new TreeBuilder('keying'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(false)->end()
                ->enumNode('size')
                    ->values(['1000px', '1500px', '2000px', '2500px'])
                    ->defaultValue('1500px')
                    ->end()
                ->enumNode('variant')
                    ->values(['marvinj', 'seriouslyjs'])
                    ->defaultValue('seriouslyjs')
                    ->end()
                ->scalarNode('seriouslyjs_color')->defaultValue('#62af74')->end()
                ->scalarNode('background_path')->defaultValue('resources/img/background')->end()
                ->booleanNode('show_all')->defaultValue(false)->end()
            ->end();
    }

    protected function addBackground(): NodeDefinition
    {
        return (new TreeBuilder('background'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('defaults')->defaultValue('')->end()
                ->scalarNode('chroma')->defaultValue('')->end()
                ->scalarNode('admin')->defaultValue('')->end()
            ->end();
    }

    protected function addIcons(): NodeDefinition
    {
        return (new TreeBuilder('icons'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('admin_back')->defaultValue('fa fa-long-arrow-left')->end()
                ->scalarNode('admin_back_short')->defaultValue('fa fa-arrow-left')->end()
                ->scalarNode('admin_menutoggle')->defaultValue('fa fa-bars')->end()
                ->scalarNode('admin_save')->defaultValue('fa fa-circle-notch fa-spin fa-fw')->end()
                ->scalarNode('admin_save_success')->defaultValue('fa fa-check')->end()
                ->scalarNode('admin_save_error')->defaultValue('fa fa-times')->end()
                ->scalarNode('admin_signout')->defaultValue('fa fa-sign-out')->end()
                ->scalarNode('admin')->defaultValue('fa fa-cog')->end()
                ->scalarNode('home')->defaultValue('fa fa-house')->end()
                ->scalarNode('gallery')->defaultValue('fa fa-image')->end()
                ->scalarNode('dependencies')->defaultValue('fa fa-list-ul')->end()
                ->scalarNode('update')->defaultValue('fa fa-tasks')->end()
                ->scalarNode('slideshow')->defaultValue('fa fa-play')->end()
                ->scalarNode('chromaCapture')->defaultValue('fa fa-paint-brush')->end()
                ->scalarNode('faq')->defaultValue('fa fa-question-circle')->end()
                ->scalarNode('manual')->defaultValue('fa fa-info-circle')->end()
                ->scalarNode('telegram')->defaultValue('fa-brands fa-telegram')->end()
                ->scalarNode('cups')->defaultValue('fa fa-print')->end()
                ->scalarNode('take_picture')->defaultValue('fa fa-camera')->end()
                ->scalarNode('take_collage')->defaultValue('fa fa-th-large')->end()
                ->scalarNode('take_video')->defaultValue('fa fa-video')->end()
                ->scalarNode('close')->defaultValue('fa fa-times')->end()
                ->scalarNode('refresh')->defaultValue('fa fa-arrows-rotate')->end()
                ->scalarNode('delete')->defaultValue('fa fa-trash-can')->end()
                ->scalarNode('print')->defaultValue('fa fa-print')->end()
                ->scalarNode('save')->defaultValue('fa fa-floppy-disk')->end()
                ->scalarNode('download')->defaultValue('fa fa-download')->end()
                ->scalarNode('qr')->defaultValue('fa fa-qrcode')->end()
                ->scalarNode('mail')->defaultValue('fa fa-envelope')->end()
                ->scalarNode('mail_close')->defaultValue('fa fa-times')->end()
                ->scalarNode('mail_submit')->defaultValue('fa fa-spinner fa-spin')->end()
                ->scalarNode('filter')->defaultValue('fa fa-wand-magic-sparkles')->end()
                ->scalarNode('chroma')->defaultValue('fa fa-paint-brush')->end()
                ->scalarNode('fullscreen')->defaultValue('fa fa-maximize')->end()
                ->scalarNode('share')->defaultValue('fa fa-share-alt')->end()
                ->scalarNode('zoom')->defaultValue('fa fa-search-plus')->end()
                ->scalarNode('logout')->defaultValue('fa fa-right-from-bracket')->end()
                ->scalarNode('date')->defaultValue('fa fa-clock')->end()
                ->scalarNode('spinner')->defaultValue('fa fa-cog fa-spin')->end()
                ->scalarNode('update_git')->defaultValue('fa fa-play-circle')->end()
                ->scalarNode('password_visibility')->defaultValue('fa fa-eye')->end()
                ->scalarNode('password_toggle')->defaultValue('fa-eye fa-eye-slash')->end()
                ->scalarNode('slideshow_play')->defaultValue('fa fa-play')->end()
                ->scalarNode('slideshow_toggle')->defaultValue('fa-play fa-pause')->end()
                ->scalarNode('take_custom')->defaultValue('fa fa-paint-brush')->end()
            ->end();
    }

    protected function addPreview(): NodeDefinition
    {
        return (new TreeBuilder('preview'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->enumNode('mode')
                    ->values(['none', 'device_cam', 'url'])
                    ->defaultValue('none')
                    ->end()
                ->booleanNode('camTakesPic')->defaultValue(false)->end()
                ->enumNode('style')
                    ->values(['none', 'device_cam', 'url'])
                    ->defaultValue('none')
                    ->end()
                ->integerNode('stop_time')
                    ->defaultValue(2)
                    ->min(0)
                    ->max(10)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->enumNode('style')
                    ->values(['fill', 'contain', 'cover', 'none', 'scale-down'])
                    ->defaultValue('scale-down')
                    ->end()
                ->enumNode('flip')
                    ->values(['off', 'horizontal', 'vertical', '1080px', 'both'])
                    ->defaultValue('off')
                    ->end()
                ->enumNode('rotation')
                    ->values(['0deg', '90deg', '-90deg', '180deg', '45deg', '-45deg'])
                    ->defaultValue('0deg')
                    ->end()
                ->scalarNode('url')->defaultValue('')->end()
                ->integerNode('url_delay')
                    ->defaultValue(1000)
                    ->min(0)
                    ->max(30000)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('videoWidth')
                    ->defaultValue(1280)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('videoHeight')
                    ->defaultValue(720)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->enumNode('camera_mode')
                    ->values(['user', 'environment'])
                    ->defaultValue('user')
                    ->end()
                ->booleanNode('asBackground')->defaultValue(false)->end()
                ->booleanNode('showFrame')->defaultValue(false)->end()
                ->booleanNode('simpleExec')->defaultValue(true)->end()
                ->booleanNode('bsm')->defaultValue(true)->end()
            ->end();
    }

    protected function addColors(): NodeDefinition
    {
        return (new TreeBuilder('colors'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('countdown')->defaultValue('#1b3faa')->end()
                ->scalarNode('background_countdown')->defaultValue('#8d9fd4')->end()
                ->scalarNode('cheese')->defaultValue('#aa1b3f')->end()
                ->scalarNode('primary')->defaultValue('#1b3faa')->end()
                ->scalarNode('primary_light')->defaultValue('#e8ebf6')->end()
                ->scalarNode('secondary')->defaultValue('#5f78c3')->end()
                ->scalarNode('highlight')->defaultValue('#8d9fd4')->end()
                ->scalarNode('font')->defaultValue('#c9c9c9')->end()
                ->scalarNode('font_secondary')->defaultValue('#333333')->end()
                ->scalarNode('button_font')->defaultValue('#ffffff')->end()
                ->scalarNode('start_font')->defaultValue('#333333')->end()
                ->scalarNode('panel')->defaultValue('#1b3faa')->end()
                ->scalarNode('border')->defaultValue('#eeeeee')->end()
                ->scalarNode('box')->defaultValue('#e8ebf6')->end()
                ->scalarNode('gallery_button')->defaultValue('#ffffff')->end()
            ->end();
    }

    protected function addProtect(): NodeDefinition
    {
        return (new TreeBuilder('protect'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('admin')->defaultValue(true)->end()
                ->booleanNode('localhost_admin')->defaultValue(true)->end()
                ->booleanNode('index')->defaultValue(false)->end()
                ->booleanNode('localhost_index')->defaultValue(false)->end()
                ->scalarNode('index_redirect')->defaultValue('login')->end()
                ->booleanNode('manual')->defaultValue(false)->end()
                ->booleanNode('localhost_manual')->defaultValue(false)->end()
            ->end();
    }

    protected function addGetRequests(): NodeDefinition
    {
        return (new TreeBuilder('get_request'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('countdown')->defaultValue(false)->end()
                ->booleanNode('processed')->defaultValue(false)->end()
                ->scalarNode('server')->defaultValue('')->end()
                ->scalarNode('picture')->defaultValue('CNTDWNPHOTO')->end()
                ->scalarNode('collage')->defaultValue('CNTDWNCOLLAGE')->end()
                ->scalarNode('video')->defaultValue('CNTDWNVIDEO')->end()
                ->scalarNode('custom')->defaultValue('CNTDWNCUSTOM')->end()
            ->end();
    }

    protected function addGallery(): NodeDefinition
    {
        return (new TreeBuilder('gallery'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(true)->end()
                ->booleanNode('newest_first')->defaultValue(true)->end()
                ->booleanNode('use_slideshow')->defaultValue(true)->end()
                ->integerNode('pictureTime')
                    ->defaultValue(3000)
                    ->min(1000)
                    ->max(10000)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->booleanNode('show_date')->defaultValue(true)->end()
                ->scalarNode('date_format')->defaultValue('d.m.Y - G:i')->end()
                ->booleanNode('db_check_enabled')->defaultValue(true)->end()
                ->integerNode('db_check_time')
                    ->defaultValue(10)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->booleanNode('allow_delete')->defaultValue(true)->end()
                ->booleanNode('scrollbar')->defaultValue(false)->end()
                ->booleanNode('bottom_bar')->defaultValue(true)->end()
                ->booleanNode('figcaption')->defaultValue(true)->end()
            ->end();
    }

    protected function addVideo(): NodeDefinition
    {
        return (new TreeBuilder('video'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(false)->end()
                ->integerNode('cntdwn_time')
                    ->defaultValue(3)
                    ->min(0)
                    ->max(10)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->scalarNode('cheese')->defaultValue('Show your moves!')->end()
                ->booleanNode('collage')->defaultValue(false)->end()
                ->booleanNode('collage_keep_images')->defaultValue(false)->end()
                ->booleanNode('collage_only')->defaultValue(false)->end()
                ->enumNode('effects')
                    ->values(['none', 'boomerang'])
                    ->defaultValue('none')
                    ->end()
                ->booleanNode('animation')->defaultValue(true)->end()
                ->booleanNode('gif')->defaultValue(false)->end()
                ->booleanNode('qr')->defaultValue(true)->end()
            ->end();
    }

    protected function addPhotoSwipe(): NodeDefinition
    {
        return (new TreeBuilder('pswp'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('counterEl')->defaultValue(true)->end()
                ->booleanNode('caption')->defaultValue(true)->end()
                ->booleanNode('clickToCloseNonZoomable')->defaultValue(false)->end()
                ->booleanNode('pinchToClose')->defaultValue(true)->end()
                ->booleanNode('closeOnVerticalDrag')->defaultValue(true)->end()
                ->booleanNode('zoomEl')->defaultValue(false)->end()
                ->booleanNode('loop')->defaultValue(true)->end()
                ->floatNode('bgOpacity')
                    ->defaultValue(1.0)
                    ->max(1)
                    ->min(0)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): float { return floatval($value); })
                        ->end()
                    ->end()
                ->scalarNode('imageClickAction')->defaultValue('toggle-controls')->end()
                ->scalarNode('tapAction')->defaultValue('toggle-controls')->end()
                ->scalarNode('doubleTapAction')->defaultValue('zoom')->end()
                ->scalarNode('bgClickAction')->defaultValue('none')->end()
            ->end();
    }

    protected function addFtp(): NodeDefinition
    {
        return (new TreeBuilder('ftp'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(false)->end()
                ->scalarNode('baseURL')->defaultValue('')->end()
                ->integerNode('port')
                    ->defaultValue(21)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->scalarNode('username')->defaultValue('')->end()
                ->scalarNode('password')->defaultValue('')->end()
                ->scalarNode('baseFolder')->defaultValue('')->end()
                ->scalarNode('folder')->defaultValue('')->end()
                ->scalarNode('title')->defaultValue('')->end()
                ->booleanNode('appendDate')->defaultValue(false)->end()
                ->booleanNode('useForQr')->defaultValue(false)->end()
                ->scalarNode('website')->defaultValue('')->end()
                ->scalarNode('urlTemplate')->defaultValue('')->end()
                ->booleanNode('create_webpage')->defaultValue(false)->end()
                ->scalarNode('template_location')->defaultValue('')->end()
                ->booleanNode('upload_thumb')->defaultValue(false)->end()
                ->booleanNode('delete')->defaultValue(false)->end()
            ->end();
    }

    protected function addLogin(): NodeDefinition
    {
        return (new TreeBuilder('login'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(false)->end()
                ->scalarNode('username')->defaultValue('Photo')->end()
                ->scalarNode('password')
                    ->defaultNull()
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): ?string { return strlen(trim($value)) === 0 ? null : $value; })
                        ->end()
                    ->end()
                ->booleanNode('keypad')->defaultValue(false)->end()
                ->scalarNode('pin')
                    ->defaultNull()
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): ?string { return strlen(trim($value)) === 0 ? null : $value; })
                        ->end()
                    ->end()
                ->booleanNode('rental_keypad')->defaultValue(false)->end()
                ->scalarNode('rental_pin')
                    ->defaultNull()
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): ?string { return strlen(trim($value)) === 0 ? null : $value; })
                        ->end()
                    ->end()
            ->end();
    }

    protected function addAdminPanel(): NodeDefinition
    {
        return (new TreeBuilder('adminpanel'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->enumNode('view')
                    ->values(['basic', 'advanced', 'expert'])
                    ->defaultValue('basic')
                    ->end()
                ->booleanNode('experimental_settings')->defaultValue(false)->end()
            ->end();
    }

    protected function addUiNode(): NodeDefinition
    {
        return (new TreeBuilder('ui'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->enumNode('language')
                    ->values(['cs', 'de', 'en', 'es', 'fr', 'hr', 'it', 'nl', 'pt'])
                    ->defaultValue('en')
                    ->end()
                ->integerNode('notification_timeout')
                    ->defaultValue(5)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->booleanNode('show_fork')->defaultValue(true)->end()
                ->booleanNode('skip_welcome')->defaultValue(false)->end()
                ->booleanNode('admin_shortcut')->defaultValue(true)->end()
                ->enumNode('admin_shortcut_position')
                    ->values(['top-left', 'top-right', 'bottom-left', 'bottom-right'])
                    ->defaultValue('bottom-right')
                    ->end()
                ->enumNode('style')
                    ->values(['classic', 'classic_rounded', 'modern', 'modern_squared', 'custom'])
                    ->defaultValue('modern_squared')
                    ->end()
                ->enumNode('button')
                    ->values(['classic', 'classic_rounded', 'modern', 'modern_squared', 'custom'])
                    ->defaultValue('modern_squared')
                    ->end()
                ->booleanNode('shutter_animation')->defaultValue(true)->end()
                ->scalarNode('shutter_cheese_img')->defaultValue('')->end()
                ->booleanNode('result_buttons')->defaultValue(true)->end()
                ->scalarNode('font_size')->defaultValue('16px')->end()
                ->booleanNode('decore_lines')->defaultValue(true)->end()
                ->booleanNode('rounded_corners')->defaultValue(false)->end()
            ->end();
    }

    protected function addDev(): NodeDefinition
    {
        return (new TreeBuilder('dev'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->integerNode('loglevel')
                    ->defaultValue(1)
                    ->min(0)
                    ->max(2)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->booleanNode('demo_images')
                    ->defaultValue(false)
                    ->end()
                ->booleanNode('reload_on_error')
                    ->defaultValue(true)
                    ->end()
            ->end();
    }

    protected function addWebserver(): NodeDefinition
    {
        return (new TreeBuilder('webserver'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('ip')->defaultValue('')->end()
                ->scalarNode('ssid')->defaultValue('Photobooth')->end()
            ->end();
    }

    protected function addStartScreen(): NodeDefinition
    {
        return (new TreeBuilder('start_screen'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('title')->defaultValue('')->end()
                ->booleanNode('title_visible')->defaultValue(false)->end()
                ->scalarNode('subtitle')->defaultValue('')->end()
                ->booleanNode('subtitle_visible')->defaultValue(false)->end()
            ->end();
    }

    protected function addLogo(): NodeDefinition
    {
        return (new TreeBuilder('logo'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(true)->end()
                ->scalarNode('path')->defaultValue('')->end()
                ->enumNode('position')
                    ->values(['center', 'top_right', 'top_left', 'bottom_right', 'bottom_left'])
                    ->defaultValue('center')
                    ->end()
            ->end();
    }

    protected function addDownload(): NodeDefinition
    {
        return (new TreeBuilder('download'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(true)->end()
                ->booleanNode('thumbs')->defaultValue(false)->end()
            ->end();
    }

    protected function addReload(): NodeDefinition
    {
        return (new TreeBuilder('reload'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('key')
                    ->info('specify key id (e.g. 13 is the enter key) to use that key to reload the page, use for example https://keycode.info to get the key code')
                    ->defaultValue('')
                    ->end()
            ->end();
    }

    protected function addPicture(): NodeDefinition
    {
        return (new TreeBuilder('picture'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('key')
                    ->info('specify key id (e.g. 13 is the enter key) to use that key to reload the page, use for example https://keycode.info to get the key code')
                    ->defaultValue('')
                    ->end()
                ->enumNode('thumb_size')
                    ->values(['360px', '540px', '900px', '1080px', '1260px'])
                    ->defaultValue('540px')
                    ->end()
                ->integerNode('time_to_live')
                    ->defaultValue(90)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->booleanNode('preview_before_processing')->defaultValue(false)->end()
                ->integerNode('retry_on_error')
                    ->defaultValue(0)
                    ->min(0)
                    ->max(10)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('retry_timeout')
                    ->defaultValue(2)
                    ->min(0)
                    ->max(10)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->booleanNode('enabled')->defaultValue(true)->end()
                ->integerNode('cntdwn_time')
                    ->defaultValue(5)
                    ->min(0)
                    ->max(10)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('cheese_time')
                    ->defaultValue(1000)
                    ->min(250)
                    ->max(10000)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->enumNode('flip')
                    ->values(['off', 'horizontal', 'vertical', '1080px', 'both'])
                    ->defaultValue('off')
                    ->end()
                ->integerNode('rotation')
                    ->defaultValue(0)
                    ->min(0)
                    ->max(359)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->booleanNode('polaroid_effect')->defaultValue(false)->end()
                ->integerNode('polaroid_rotation')
                    ->defaultValue(0)
                    ->min(0)
                    ->max(359)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->booleanNode('take_frame')->defaultValue(true)->end()
                ->scalarNode('frame')->defaultValue('')->end()
                ->booleanNode('extend_by_frame')->defaultValue(true)->end()
                ->integerNode('frame_left_percentage')
                    ->defaultValue(10)
                    ->min(0)
                    ->max(100)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('frame_right_percentage')
                    ->defaultValue(10)
                    ->min(0)
                    ->max(100)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('frame_top_percentage')
                    ->defaultValue(5)
                    ->min(0)
                    ->max(100)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('frame_bottom_percentage')
                    ->defaultValue(15)
                    ->min(0)
                    ->max(100)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->enumNode('naming')
                    ->values(['dateformatted', 'random'])
                    ->defaultValue('dateformatted')
                    ->end()
                ->scalarNode('permissions')
                    ->info('0644 (rw-r--r--), 0666 (rw-rw-rw-), 0600 (rw-------)')
                    ->defaultValue('0644')
                    ->end()
                ->booleanNode('keep_original')->defaultValue(true)->end()
                ->booleanNode('preserve_exif_data')->defaultValue(false)->end()
                ->booleanNode('allow_delete')->defaultValue(true)->end()
            ->end();
    }

    protected function addTextOnPicture(): NodeDefinition
    {
        return (new TreeBuilder('textonpicture'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(false)->end()
                ->scalarNode('line1')->defaultValue('line 1')->end()
                ->scalarNode('line2')->defaultValue('line 2')->end()
                ->scalarNode('line3')->defaultValue('line 3')->end()
                ->integerNode('locationx')
                    ->defaultValue(80)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('locationy')
                    ->defaultValue(80)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('rotation')
                    ->defaultValue(0)
                    ->min(0)
                    ->max(359)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->scalarNode('font')->defaultValue('')->end()
                ->scalarNode('font_color')->defaultValue('#ffffff')->end()
                ->integerNode('font_size')
                    ->defaultValue(80)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('linespace')
                    ->defaultValue(90)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
            ->end();
    }

    protected function addDatabase(): NodeDefinition
    {
        return (new TreeBuilder('database'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(true)->end()
                ->scalarNode('file')->defaultValue('db')->end()
            ->end();
    }

    protected function addDelete(): NodeDefinition
    {
        return (new TreeBuilder('delete'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('no_request')->defaultValue(false)->end()
            ->end();
    }

    protected function addEvent(): NodeDefinition
    {
        return (new TreeBuilder('event'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(false)->end()
                ->scalarNode('textRight')->defaultValue('')->end()
                ->scalarNode('textLeft')->defaultValue('')->end()
                ->enumNode('symbol')
                    ->values([
                        'fa-camera-retro', 'fa-birthday-cake', 'fa-gift', 'fa-tree', 'fa-snowflake-o', 'fa-heart-o',
                        'fa-heart', 'fa-heartbeat', 'fa-apple', 'fa-anchor', 'fa-glass', 'fa-gears', 'fa-users'
                    ])
                    ->defaultValue('fa-heart-o')
                    ->end()
            ->end();
    }

    protected function addButton(): NodeDefinition
    {
        return (new TreeBuilder('button'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('force_buzzer')->defaultValue(false)->end()
                ->booleanNode('show_cups')->defaultValue(false)->end()
                ->booleanNode('show_fs')->defaultValue(false)->end()
                ->booleanNode('homescreen')->defaultValue(true)->end()
                ->booleanNode('reload')->defaultValue(false)->end()
            ->end();
    }

    protected function addFilters(): NodeDefinition
    {
        return (new TreeBuilder('filters'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')
                    ->defaultValue(true)
                    ->end()
                ->enumNode('defaults')
                    ->values(ImageFilterEnum::cases())
                    ->defaultValue(ImageFilterEnum::PLAIN)
                    ->beforeNormalization()
                        ->always(function ($value) {
                            if (is_string($value)) {
                                $value = ImageFilterEnum::from($value);
                            }
                            return $value;
                        })
                        ->end()
                    ->end()
                ->arrayNode('disabled')
                    ->enumPrototype()
                        ->values(ImageFilterEnum::cases())
                        ->beforeNormalization()
                            ->always(function ($value) {
                                if (is_string($value)) {
                                    $value = ImageFilterEnum::from($value);
                                }
                                return $value;
                            })
                            ->end()
                        ->end()
                    ->end()
            ->end();
    }

    protected function addCustom(): NodeDefinition
    {
        return (new TreeBuilder('custom'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')
                    ->defaultValue(false)
                    ->end()
                ->integerNode('cntdwn_time')
                    ->defaultValue(5)
                    ->min(0)
                    ->max(10)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->scalarNode('key')
                    ->info('specify key id (e.g. 13 is the enter key) to use that key to reload the page, use for example https://keycode.info to get the key code')
                    ->defaultValue('')
                    ->end()
                ->scalarNode('btn_text')
                    ->defaultValue('Custom')
                    ->end()
            ->end();
    }

    protected function addCollage(): NodeDefinition
    {
        return (new TreeBuilder('collage'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(true)->end()
                ->integerNode('cntdwn_time')
                    ->defaultValue(3)
                    ->min(0)
                    ->max(10)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->booleanNode('continuous')->defaultValue(true)->end()
                ->integerNode('continuous_time')
                    ->defaultValue(5)
                    ->min(0)
                    ->max(20)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->enumNode('layout')
                    ->values([
                        '2+2', '2+2-2', '1+3', '1+3-2', '3+1', '1+2', '2+1',
                        '2x4', '2x4-2', '2x4-3', '2x4-4', '2x3', '2x3-2',
                        'collage.json',
                    ])
                    ->defaultValue('2+2-2')
                    ->end()
                ->enumNode('resolution')
                    ->values(['150dpi', '300dpi', '400dpi', '600dpi'])
                    ->defaultValue('300dpi')
                    ->end()
                ->scalarNode('dashedline_color')->defaultValue('#000000')->end()
                ->booleanNode('keep_single_images')->defaultValue(false)->end()
                ->scalarNode('key')
                    ->info('specify key id (e.g. 13 is the enter key) to use that key to reload the page, use for example https://keycode.info to get the key code')
                    ->defaultValue('')
                    ->end()
                ->scalarNode('background_color')->defaultValue('#ffffff')->end()
                ->enumNode('take_frame')
                    ->values(['off', 'always', 'once'])
                    ->defaultValue('off')
                    ->end()
                ->scalarNode('frame')->defaultValue('')->end()
                ->booleanNode('placeholder')->defaultValue(false)->end()
                ->integerNode('placeholderposition')
                    ->defaultValue(1)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->scalarNode('placeholderpath')->defaultValue('')->end()
                ->scalarNode('background')->defaultValue('')->end()
                ->integerNode('limit')
                    ->defaultValue(4)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
            ->end();
    }

    protected function addTextOnCollage(): NodeDefinition
    {
        return (new TreeBuilder('textoncollage'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultValue(true)->end()
                ->scalarNode('line1')->defaultValue('Photobooth')->end()
                ->scalarNode('line2')->defaultValue('   we love')->end()
                ->scalarNode('line3')->defaultValue('OpenSource')->end()
                ->integerNode('locationx')
                    ->defaultValue(1470)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('locationy')
                    ->defaultValue(250)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('rotation')
                    ->defaultValue(0)
                    ->min(0)
                    ->max(359)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->scalarNode('font')->defaultValue('')->end()
                ->scalarNode('font_color')->defaultValue('#000000')->end()
                ->integerNode('font_size')
                    ->defaultValue(50)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('linespace')
                    ->defaultValue(90)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
            ->end();
    }

    protected function addQuality(): NodeDefinition
    {
        return (new TreeBuilder('jpeg_quality'))->getRootNode()->addDefaultsIfNotSet()
            ->children()
                ->integerNode('image')
                    ->defaultValue(100)
                    ->max(100)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('chroma')
                    ->defaultValue(100)
                    ->max(100)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
                ->integerNode('thumb')
                    ->defaultValue(60)
                    ->max(100)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                        ->end()
                    ->end()
            ->end();
    }
}
