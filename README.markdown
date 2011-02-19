##Symfony2Project


##Usage

    symfony2project generate:project AppName VendorName Path [--controller[="..."]] [--protocol[="..."]] [--session-start[="..."]] [--session-name[="..."]] [--symfony-repository[="..."]] [--orm[="..."]] [--odm[="..."]] [--assetic] [--swiftmailer] [--doctrine-migration] [--doctrine-fixtures] [--template-engine[="..."]]

###Arguments

    AppName                : application name (mandatory)
    VendorName             : vendor name (mandatory)
    Path                   : directory name (path)

###Options

    --controller           : Your first controller name
    --protocol             : git or http (default: git)
    --session-start        : false or true (default: false)
    --session-name         : Session name (default: symfony)
    --symfony-repository   : fabpot or symfony (default: symfony)
    --orm                  : doctrine or propel
    --odm                  : mongodb
    --assetic              : Enable assetic
    --swiftmailer          : Enable swiftmailer
    --doctrine-migration   : Enable doctrine migration
    --doctrine-fixtures    : Enable doctrine fixtures
    --template-engine      : twig or php (default: twig)


*Note: only tested on unix system.*
