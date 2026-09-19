***these files go outside of the site folder...***

### standard file structure

```
[...]/anorrl/ (root)

 - assets/
 |- thumbs/
 L_ 3d/

 - site

 - users/
 |- renders/
 |-- headshots/
 |-- 3d/
 L_ profiles/
 ```

these folders should be generated automatically (just give the parent folder of the site same permissions as the webhost so that it can actually create them)

### what needs to be in the root...

- `settings.json`
- `PrivateKey.pem` (generated from RBXSIGTOOLS)

### info..

settings.json HAS to be changed before being used for production. It is just a template right now.
