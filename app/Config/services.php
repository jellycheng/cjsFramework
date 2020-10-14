<?php
App::singleton('log', function() {
    //保存7天日志文件
    $fh = new \Monolog\Handler\RotatingFileHandler(
        Config::get('log.prefix'),
        0,
        Config::get('log.level', 'debug'),
        false
    );
    
    $fh->setFilenameFormat('{filename}.{date}', 'Y-m-d');
    $formatter = new \Monolog\Formatter\LineFormatter("%datetime% %millisecond% %channel% %level_name% %request_id% %message% %context%\n");//日志内容格式
    $fh->setFormatter($formatter);//文件对象设置内容格式对象

    //return new \Monolog\Logger(Config::get('log.name', 'app'), array($fh));
    return new \App\Util\Logger(Config::get('log.channel', 'mobile-api'), array($fh));
    
});

App::singleton('validator', function() {
    //$translator = new \Symfony\Component\Translation\Translator('en');
    $filesystemObj = new \Illuminate\Filesystem\Filesystem();
    $translationLoader = new \Illuminate\Translation\FileLoader($filesystemObj, 'lang');
    $translator = new \Illuminate\Translation\Translator($translationLoader, 'en');
    $translator->setFallback('en');
    return new \Illuminate\Validation\Factory($translator);
});


App::singleton('events', function () {
    return new \Illuminate\Events\Dispatcher();
});

