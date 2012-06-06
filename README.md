### flexTable

Organize any content in structured and flexible tables. Add detail informations, easy add and remove columns and rows. Access to the informations at any WYSIWYG section with Droplets.

Admin-Tool for the Content Management System [WebsiteBaker] [1] or [LEPTON CMS] [2].

#### Requirements

* minimum PHP 5.2.x
* using [WebsiteBaker] [1] _or_ using [LEPTON CMS] [2]
* [dbConnect_LE] [4] must be installed
* [Dwoo] [3] must be installed 
* [DropletsExtension] [5] must be installed
* [kitTools] [6] must be installed
* [permaLink] [7] must be installed

#### Installation

* download the actual [flexTable_x.xx.zip] [8] installation archive
* in CMS backend select the file from "Add-ons" -> "Modules" -> "Install module"

#### First Steps

In the CMS backend select "Admin-Tools" -> "flexTable" and click to "Edit" to create a new table.

You will find hints to each required field. Fill out the mandantory fields, i.e. ** and add some **datafields**, save the table and start to add **items**.

Now change to a WYSIWYG page and insert this Droplet code:

    [[flex_table?name=<IDENTIFIER>]]
    
The &lt;IDENTIFIER> is the identifer you have given your table.

That's all!

Please visit the [phpManufaktur] [9] to get more informations about **flexTable**.

Use the [phpManufaktur - General Addons Support Group] [10] to get support.

[1]: http://websitebaker2.org "WebsiteBaker Content Management System"
[2]: http://lepton-cms.org "LEPTON CMS"
[3]: https://github.com/phpManufaktur/Dwoo/downloads
[4]: https://github.com/phpManufaktur/dbConnect_LE/downloads
[5]: https://github.com/phpManufaktur/DropletsExtension/downloads
[6]: https://github.com/phpManufaktur/kitTools/downloads
[7]: https://github.com/phpManufaktur/permaLink/downloads
[8]: https://github.com/phpManufaktur/flexTable/downloads
[9]: https://phpmanufaktur.de
[10]: https://phpmanufaktur.de/support
