##Symfony2Project


##Usage
 
    php symfony2project.php --app=AppName [--path=/your/destination/path] [--controller=controllerName] [--protocol=git|http] [--session-start=false|true] [--session-name=sessionName] [--symfony-repository=fabpot|symfony] [--with-db=false|true]

###Arguments

    --app                : application name (mandatory)
    --path               : directory name (path) (default: current dir)
    --controller         : your first controller name (optional)
                           (suggestion: home or main, you can change it later if you change your mind)
    --protocol           : git or http (if git is not enable in your company)
    --session-start      : false or true (auto_start parameter on session) (default: false)
    --session-name       : Session name (default: Application name)
    --symfony-repository : fabpot or symfony (default: symfony)
    --with-db:           : false or true (default: true)

*Note: only tested on unix system.*

2010, Bertrand Zuchuat

