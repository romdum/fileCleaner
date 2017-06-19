# fileCleaner

fileCleaner is a short PHP script which will clean your document automaticly.


## Getting started

Clone the project :

```
git clone https://github.com/romdum/fileCleaner.git
```

To launch the script :

```
php path/to/script/fileCleaner.php
```

### Configure settings

Settings are group in settings.json file, it format is :

```
{
    "/path/to/directory/to/clean/" : {
        "toRemove" : ["fileExtension1","fileExtension2"],
        "toMove" : [
            {"/path/destination" : "fileExtension1"},
            {"/path/destination" : "fileExtension2"}
        ],
        "toKeep" : [
            {"timeToKeep" : "fileExtension"},
            {"timeToKeep" : "fileExtension2"}
        ]
    }
}
```

* `toRemove` array contain files extensions which will be remove.
* `toMove` array contain where the file will be move (key) and files extensions to move.
* `toKeep` array contain the time to keep a file (key) and files extensions to remove.
* `toCopy` array contain where the file will be copy (key) and files extensions to copy.

**Warning** when ou use to copy if the file already exist in destination folder, copy will not be done.

Here is an example :

```
{
    "/home/user/Téléchargements/" : {
        "toRemove" : ["deb","torrent"],
        "toMove" : [
            {"/home/user/Images" : "jpg"},
            {"/home/user/Images" : "jpeg"},
            {"/home/user/Images" : "png"},
            {"/home/user/Images" : "gif"},
            {"/home/user/Vidéos" : "mp4"},
            {"/home/user/Vidéos" : "folder.mp4"},
            {"/home/user/Vidéos" : "avi"},
            {"/home/user/Musiques" : "mp3"},
            {"/home/user/Documents" : "pdf"},
            {"/home/user/Documents" : "odt"}
        ],
        "toKeep" : [
            {"2 week" : "zip"},
            {"2 week" : "tar.gz"},
            {"2 week" : "rar"},
            {"2 week" : "gz"},
            {"2 week" : "tar"},
            {"4 week" : "txt"}
        ]
    },
    "/home/user/Vidéos/" : {
        "toRemove" : ["torrent"],
        "toKeep" : [
            {"8 week" : "avi"},
            {"8 week" : "mp4"},
            {"8 week" : "folder"}
        ],
        "toCopy" : [
            {"/home/user/Bureau" : "txt"}
        ]
    }
}

```

You can use `folder` extension to remove, move or keep folders.
You can use `folder.extension` to move or keep folder containing extension, for example :

```
"toMove" : [
    {"/home/user/Vidéos" : "folder.mp4"},
]
```

That setting will move folders containing mp4 files in "/home/user/Vidéos" directory.

------

## Tips and Tricks

The time to keep is use with the [strtotime](http://php.net/manual/en/function.strtotime.php) PHP function, you can refer to it to personnalize this value.
