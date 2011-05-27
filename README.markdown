##Symfony2Project


##Usage

    symfony2project generate:project AppName VendorName Path [--controller="..."] [--protocol="..."]
    [--session-start] [--session-name="..."] [--orm="..."] [--odm="..."] [--assetic] [--swiftmailer]
    [--doctrine-migration] [--doctrine-fixtures] [--template-engine="..."] [--profile="..."]
    [--assets-symlink] [--force-delete]

###Arguments

    AppName                : application name (mandatory)
    VendorName             : vendor name (mandatory)
    Path                   : directory name (path)

###Options

    --controller           : Your first controller name (default: Main)
    --protocol             : git or http (default: git)
    --session-start        : To start session automatically
    --session-name         : Session name (default: symfony)
    --orm                  : doctrine or propel
    --odm                  : mongodb or mandango
    --assetic              : Enable assetic
    --swiftmailer          : Enable swiftmailer
    --doctrine-migration   : Enable doctrine migration
    --doctrine-fixtures    : Enable doctrine fixtures
    --template-engine      : twig or php (default: twig)
    --profile              : Profile name (default: default) or http url
    --assets-symlink       : Symlink for web assets
    --force-delete         : Force re-generation of project

###Profile

    Before execute this command, copy the file Resources/Profile/default.xml.dist
    to Resources/Profile/default.xml
    
    if you would like to personalize, open this file, modify or add some parameters.
    
    You can also create new profile, copy the file default.xml.dist, rename (ex: foo.xml) and use --profile=foo on command line.

    http server example: http://myserver/profile/default (not with xml)

*Note: only tested on unix system.*
