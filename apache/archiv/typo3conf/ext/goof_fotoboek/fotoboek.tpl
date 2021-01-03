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
DIRPATH	= will show a linked Rootline Navigation like: / Folder1 / SubFolder2 / SubSubFolder3
THUMBNAIL_IMAGE
THUMBNAIL_COMMMENT_HEADER
THUMBNAIL_FILESIZE
ORIENTATION
THUMBID
COUNT_NR
COUNT_COUNT
COUNT_STRING
PAGES

<!-- ###SINGLE### start -->
<table border="0">
<tr><td align="left">###DIRTITLE###
###NAVSTART######PREV######SPACER######INDEX######SPACER######UP######SPACER######THUMBS######SPACER######NEXT######SPACER######SLIDESHOW######NAVEND###<br/>
###DIRS###
<br />
###IMAGE###<br/>
<b>###TITLE###</b><br/>
###COMMENT###
</td></tr></table>
<!-- ###SINGLE### stop -->

<!-- ###THUMBTPL### start -->
<table border="0">
<tr><td colspan="11" align="left">
###DIRTITLE###
###NAVSTART######PREV######SPACER######INDEX######SPACER######UP######SPACER######NEXT######NAVEND###<br/>
###DIRS###<br/>
</td></tr>
###THUMBNAILS###

</table>
<!-- ###THUMBTPL### stop -->

<!-- ###THUMBNAIL### start -->
<td>
	###THUMBNAIL_IMAGE###<br />
<!-- 	###THUMBNAIL_COMMMENT_HEADER###<br />
	###THUMBNAIL_FILESIZE###<br />
	###THUMBNAIL_FILENAME###<br />
	###THUMBID###<br />
	###ORIENTATION###<br /> -->
</td>
<!-- ###THUMBNAIL### stop -->

<!-- ###COMBINETPL### start -->
<tr><td colspan="11" align="left">
###DIRTITLE###
###NAVSTART######PREV######SPACER######INDEX######SPACER######UP######SPACER######NEXT######NAVEND###<br/>
###DIRS###<br/>
</td></tr>
###THUMBNAILS###
</table>
###IMAGE###<br/>
<b>###TITLE###</b><br/>
###COMMENT###
###EXTRA###
<!-- ###COMBINETPL### stop -->
