SINGLE 	= template for single picture
THUMBTPL	= template for the thumbnails	
THUMBNAIL	= section for a single thumbnail (needs to be enabled in the config)

DIRTITLE	= Name of current directory or the comment for it.
PREV		= previous button
NEXT		= next button
INDEX		= button to go to the root of the photobook
UP		= go one level up
THUMB		= thumbnails button
DIRS		= list of subdirectories or their comment
IMAGE		= the photo itself
TITLE		= first row of the photo comment file
COMMENT	= rest of the comment file
THUMBNAILS  = Thumbnail images
NAVSTART	= Start image of navigation bar
NAVEND	= End image of navigation bar
FASTNAV	= Fast navigation bar (need to be enabled in the config)
PATH_TO_ORIGINAL = relative path to the original image
PATH_TO_SINGLE = relative path to the resized image 
DIRPATH	= will show a linked Rootline Navigation like: / Folder1 / SubFolder2 / SubSubFolder3
THUMBNAIL_IMAGE
THUMBNAIL_COMMMENT_HEADER
THUMBNAIL_FILESIZE
ORIENTATION
THUMBID

<!-- ###SINGLE### start -->
<div>
###DIRTITLE###
<div>###NAVSTART######PREV######SPACER######INDEX######SPACER######UP######SPACER######THUMBS######SPACER######NEXT######SPACER######SLIDESHOW######NAVEND###</div>
###BASKET###
###ADD_TO_BASKET###<br />
###DIRS###
###IMAGE###
###TITLE###
###COMMENT###
###FASTNAV###
</div>
<!-- ###SINGLE### stop -->

<!-- ###THUMBTPL### start -->
<div>###DIRTITLE###
<div>###NAVSTART######PREV######SPACER######INDEX######SPACER######UP######SPACER######NEXT######NAVEND###</div>
###DIRS###
<div class="tabel">
###THUMBNAILS###
</div>
###PAGES###
</div>
<!-- ###THUMBTPL### stop -->

<!-- ###THUMBNAIL### start -->
<div>
	###THUMBNAIL_IMAGE###<br />
<!-- 	###THUMBNAIL_COMMMENT_HEADER###<br />
	###THUMBNAIL_FILESIZE###<br />
	###THUMBNAIL_FILENAME###<br />
	###THUMBID###<br />
	###ORIENTATION###<br /> -->
</div>
<!-- ###THUMBNAIL### stop -->

<!-- ###COMBINETPL### start -->
<div>###DIRTITLE###
<h1>This is the #COMBINETPL# template </h1>
<div>###NAVSTART######PREV######SPACER######INDEX######SPACER######UP######SPACER######NEXT######NAVEND###</div>
###DIRS###
<div class="tabel">
###THUMBNAILS###
</div>
<div class="single">
###IMAGE###
###TITLE###
###COMMENT###
</div>
</div>
<!-- ###COMBINETPL### stop -->


<!-- ###ROOTCOMBINETPL### start -->
<div>###DIRTITLE###
###DIRS###
<h1>This is the #ROOTCOMBINETPL# template </h1>
<div class="single">###IMAGE###</div>
<div class="tabel">
###THUMBNAILS###
</div>
</div>
<!-- ###ROOTCOMBINETPL### stop -->

<!-- ###COMBINE_TPL### start -->
<div>###DIRTITLE###
<h1>This is the #COMBINETPL# template </h1>
<div>###NAVSTART######PREV######SPACER######INDEX######SPACER######UP######SPACER######NEXT######NAVEND###</div>
###DIRS###
<div class="tabel">
###THUMBNAILS###
</div>
<div class="single">###IMAGE###
###TITLE###
###COMMENT###
</div>
</div>
<!-- ###COMBINE_TPL### stop -->

<!-- ###BASKET_TPL### start -->
<div>
<h1>###BASKET_NAME###</h1>
###BASKET_CLOSE###<br />
###BASKET_COUNT###<br />
###BASKET_ITEMS###
<br />###BASKET_EMPTY###<br />###BASKET_GET_ZIP###</div>
<!-- ###BASKET_TPL### stop -->

<!-- ###BASKET_ITEM### start -->
<div>
###BASKET_THUMB###
###BASKET_FILENAME###
###BASKET_REMOVE###
</div>
<!-- ###BASKET_ITEM### stop -->

<!-- ###POPUP_TEMPLATE### start -->
<html><head><title>###POPUP_TITLE###</title>
</head>
<body>
###POPUP_CONTENT###
</body>
</html>
<!-- ###POPUP_TEMPLATE### stop -->
