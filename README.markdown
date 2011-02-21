##Symfony2Project


##Usage

    symfony2project generate:project AppName VendorName Path [--controller[="..."]] [--protocol[="..."]] [--session-start[="..."]] [--session-name[="..."]] [--symfony-repository[="..."]] [--orm[="..."]] [--odm[="..."]] [--assetic] [--swiftmailer] [--doctrine-migration] [--doctrine-fixtures] [--template-engine[="..."]] [--force-delete]

###Arguments

    AppName                : application name (mandatory)
    VendorName             : vendor name (mandatory)
    Path                   : directory name (path)

###Options

    --controller           : Your first controller name (default: Main)
    --protocol             : git or http (default: git)
    --session-start        : To start session automatically
    --session-name         : Session name (default: symfony)
    --symfony-repository   : fabpot or symfony (default: symfony)
    --orm                  : doctrine or propel
    --odm                  : mongodb
    --assetic              : Enable assetic
    --swiftmailer          : Enable swiftmailer
    --doctrine-migration   : Enable doctrine migration
    --doctrine-fixtures    : Enable doctrine fixtures
    --template-engine      : twig or php (default: twig)
    --force-delete         : Force re-generation of project


*Note: only tested on unix system.*
