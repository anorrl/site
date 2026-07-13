these files go outside of the site folder...

standard file structure should be:

[...]/anorrl/

 - assets/
 - - thumbs/
 - - 3d/

 - site

 - users/
 - - renders/
 - - - headshots/
 - - - 3d/
 - - profiles/

these folders should be generated automatically (just give the parent folder of the site same permissions as the webhost so that it can actually create them)

what needs to be in the root...

settings.json
PrivateKey.pem (generated from RBXSIGTOOLS but any rsa private key generator works)

some info..

anorrldb.sql is meant to be loaded in via phpmyadmin ALSO it is just the structure only ALSO it doesn't have to be named anorrldb (you can change that in settings.json)
settings.json HAS to be changed before being used for production. It is just a template right now.
