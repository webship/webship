# 11.0.0-rc1

### Highlighted important changes since Webship 11.0.0-beta1:
* task: Update the whole Webship distribution to support Drupal ~11.4.0 — the profile and all 14 `web*` component modules (Web Patches, Web Assets, Web Config, Web Security, Web SEO, Web Newsletter, Web Doc, Web Editor, Web Admin, Web Page, Web Dev, Web Blog, Web Releases, Web Theme).
* feat: Convert every Webship component module to ship a `<module>/default` recipe.
* ci: Add automated functional acceptance testing (webship-js — Playwright + Cucumber-js) across the component modules.

### Added since Webship 11.0.0-beta1:
* ci: [#3508520](https://git.drupalcode.org/project/webblog/-/work_items/3508520) Add .gitlab-ci.yml file to enable GitLab CI/CD pipeline in Webblog Module
* ci: [#3556416](https://git.drupalcode.org/project/webnewsletter/-/work_items/3556416) Add .gitlab-ci.yml file to enable GitLab CI/CD pipeline in Webnewsletter Module
* ci: [#3556429](https://git.drupalcode.org/project/webreleases/-/work_items/3556429) Add .gitlab-ci.yml file to enable GitLab CI/CD pipeline in Webreleases Module
* feat: [#3574957](https://git.drupalcode.org/project/webadmin/-/work_items/3574957) Convert Web Admin to have a webadmin/default recipe
* feat: [#3574960](https://git.drupalcode.org/project/webassets/-/work_items/3574960) Convert Web Assets to have a webassets/default recipe
* feat: [#3574990](https://git.drupalcode.org/project/webblog/-/work_items/3574990) Convert Web Blog to have a webblog/default recipe
* feat: [#3574992](https://git.drupalcode.org/project/webconfig/-/work_items/3574992) Convert Web Config to have a webconfig/default recipe
* feat: [#3574997](https://git.drupalcode.org/project/webdev/-/work_items/3574997) Convert Web Development to have a webdev/default recipe
* feat: [#3575006](https://git.drupalcode.org/project/webdoc/-/work_items/3575006) Convert Web Doc to have a webdoc/default recipe
* feat: [#3590378](https://git.drupalcode.org/project/webeditor/-/work_items/3590378) Convert Web Editor to have a webeditor/default recipe
* feat: [#3590381](https://git.drupalcode.org/project/webnewsletter/-/work_items/3590381) Convert Web Newsletter to have a webnewsletter/default recipe
* ci: [#3590406](https://git.drupalcode.org/project/webnewsletter/-/work_items/3590406) Add automated browser tests (webship-js BDD + Playwright) and GitLab CI jobs
* feat: [#3590439](https://git.drupalcode.org/project/websecurity/-/work_items/3590439) Convert Web Security to have a websecurity/default recipe
* feat: [#3590535](https://git.drupalcode.org/project/webseo/-/work_items/3590535) Convert Web SEO to have a webseo/default recipe
* feat: [#3590541](https://git.drupalcode.org/project/webreleases/-/work_items/3590541) Convert Web Releases to have a webreleases/default recipe
* ci: [#3591427](https://git.drupalcode.org/project/webreleases/-/work_items/3591427) Add automated browser tests (webship-js BDD + Playwright) and GitLab CI jobs
* feat: [#3591452](https://git.drupalcode.org/project/webpage/-/work_items/3591452) Convert Webpage to have a webpage/default recipe
* ci: [#3591467](https://git.drupalcode.org/project/webpage/-/work_items/3591467) Add automated browser tests (webship-js BDD) and GitLab CI jobs
* ci: [#3591636](https://git.drupalcode.org/project/webdoc/-/work_items/3591636) Add automated functional acceptance testing (webship-js BDD) and fix recipe install regressions surfaced by it
* ci: [#3591668](https://git.drupalcode.org/project/webreleases/-/work_items/3591668) Improve automated functional acceptance testing with user roles
* ci: [#3591679](https://git.drupalcode.org/project/webseo/-/work_items/3591679) Add automated functional acceptance testing for Web SEO with webship-js (Playwright + Cucumber-js)
* ci: [#3591693](https://git.drupalcode.org/project/websecurity/-/work_items/3591693) Add automated functional acceptance testing for Web Security with webship-js (Playwright + Cucumber-js)
* ci: [#3591725](https://git.drupalcode.org/project/webdev/-/work_items/3591725) Add automated functional acceptance testing for Web Development with webship-js (Playwright + Cucumber-js)
* ci: [#3591744](https://git.drupalcode.org/project/webconfig/-/work_items/3591744) Add automated functional acceptance testing for Web Config with webship-js (Playwright + Cucumber-js) and have the default recipe   install all bundled configuration modules
* ci: [#3591755](https://git.drupalcode.org/project/webadmin/-/work_items/3591755) Add automated functional acceptance testing for Web Admin with webship-js, fix recipe to install Gin admin theme and Masquerade
* ci: [#3592108](https://git.drupalcode.org/project/webassets/-/work_items/3592108) Add automated functional acceptance testing for Web Assets with webship-js (Playwright + Cucumber-js)
* feat: [#3592113](https://git.drupalcode.org/project/webassets/-/work_items/3592113) Add Remote image media support to Web Assets

### Changed since Webship 11.0.0-beta1:
* chore: [#3582190](https://git.drupalcode.org/project/webship/-/work_items/3582190) Update Webship to support Drupal ~11.4.0
* chore: [#3548442](https://git.drupalcode.org/project/webdev/-/work_items/3548442) Update Metatag module from ~2.1.0 to ~2.2.0
* chore: [#3548447](https://git.drupalcode.org/project/webseo/-/work_items/3548447) Update Metatag module from ~2.1.0 to ~2.2.0
* chore: [#3553224](https://git.drupalcode.org/project/webadmin/-/work_items/3553224) Update Views Bulk Operations Module from ~4.3.0 to ~4.4.0
* chore: [#3564739](https://git.drupalcode.org/project/webadmin/-/work_items/3564739) Update Drupal Core from ~11.2.0 to ~11.3.0 for Web Admin module
* chore: [#3564752](https://git.drupalcode.org/project/webassets/-/work_items/3564752) Update Drupal Core from ~11.2.0 to ~11.3.0 for Web Assets module
* chore: [#3564756](https://git.drupalcode.org/project/webblog/-/work_items/3564756) Update Drupal Core from ~11.2.0 to ~11.3.0 for Web Blog module
* chore: [#3564758](https://git.drupalcode.org/project/webconfig/-/work_items/3564758) Update Drupal Core from ~11.2.0 to ~11.3.0 for Web Config module
* chore: [#3564759](https://git.drupalcode.org/project/webdev/-/work_items/3564759) Update Drupal Core from ~11.2.0 to ~11.3.0 for Web Development module
* chore: [#3564761](https://git.drupalcode.org/project/webdoc/-/work_items/3564761) Update Drupal Core from ~11.2.0 to ~11.3.0 for Web Doc module
* chore: [#3564762](https://git.drupalcode.org/project/webeditor/-/work_items/3564762) Update Drupal Core from ~11.2.0 to ~11.3.0 for Web Editor module
* chore: [#3564764](https://git.drupalcode.org/project/webnewsletter/-/work_items/3564764) Update Drupal Core from ~11.2.0 to ~11.3.0 for Web Newsletter module
* chore: [#3564765](https://git.drupalcode.org/project/webpatches/-/work_items/3564765) Update Drupal Core from ~11.2.0 to ~11.3.0 for Web Patches module
* chore: [#3564767](https://git.drupalcode.org/project/webreleases/-/work_items/3564767) Update Drupal Core from ~11.2.0 to ~11.3.0 for Web Releases module
* chore: [#3564772](https://git.drupalcode.org/project/websecurity/-/work_items/3564772) Update Drupal Core from ~11.2.0 to ~11.3.0 for Web Security module
* chore: [#3564774](https://git.drupalcode.org/project/webseo/-/work_items/3564774) Update Drupal Core from ~11.2.0 to ~11.3.0 for Web SEO module
* chore: [#3564775](https://git.drupalcode.org/project/webpage/-/work_items/3564775) Update Drupal Core from ~11.2.0 to ~11.3.0 for Webpage module
* chore: [#3564776](https://git.drupalcode.org/project/webtheme/-/work_items/3564776) Update Drupal Core from ~11.2.0 to ~11.3.0 for Webtheme module
* chore: [#3564778](https://git.drupalcode.org/project/webtheme/-/work_items/3564778) Update Web Theme to support Drupal ~11.4.0
* chore: [#3569367](https://git.drupalcode.org/project/webpatches/-/work_items/3569367) Add a patch for the Dashboards module on fix: #3542888 PHP 8.4 Support
* chore: [#3569371](https://git.drupalcode.org/project/webpatches/-/work_items/3569371) Add a patch for the Views Bulk Edit module on Issue #3561886 : Implicit nullable AccountInterface parameters for PHP 8.4 compatibility
* chore: [#3583162](https://git.drupalcode.org/project/webpatches/-/work_items/3583162) Remove a patch for the Dashboard module on on fix: 3542888 PHP 8.4 Support
* chore: [#3590367](https://git.drupalcode.org/project/webpatches/-/work_items/3590367) Remove a patch for the View Bulk Edit on Issue 3561886 : Implicit nullable AccountInterface parameters for PHP 8.4 compatibility
* chore: [#3590368](https://git.drupalcode.org/project/webpatches/-/work_items/3590368) Update Web Patches to support Drupal ~11.4.0
* chore: [#3590386](https://git.drupalcode.org/project/webnewsletter/-/work_items/3590386) Change logo.png to use the standalone newsletter icon without the border
* chore: [#3591497](https://git.drupalcode.org/project/webpage/-/work_items/3591497) Refresh logo.png by removing the border around the inner artwork
* chore: [#3591498](https://git.drupalcode.org/project/webpage/-/work_items/3591498) Update Web Page to support Drupal ~11.4.0
* chore: [#3591552](https://git.drupalcode.org/project/webblog/-/work_items/3591552) Update Web Blog to support Drupal ~11.4.0
* chore: [#3591612](https://git.drupalcode.org/project/webnewsletter/-/work_items/3591612) Update Web Newsletter to support Drupal ~11.4.0
* chore: [#3591646](https://git.drupalcode.org/project/webdoc/-/work_items/3591646) Refresh logo.png with an open-book mark for better recognisability
* chore: [#3591647](https://git.drupalcode.org/project/webdoc/-/work_items/3591647) Update Web Doc to support Drupal ~11.4.0
* chore: [#3591669](https://git.drupalcode.org/project/webreleases/-/work_items/3591669) Update Web Releases to support Drupal ~11.4.0
* chore: [#3591680](https://git.drupalcode.org/project/webseo/-/work_items/3591680) Update Web SEO to support Drupal ~11.4.0
* chore: [#3591694](https://git.drupalcode.org/project/websecurity/-/work_items/3591694) Update Web Security to support Drupal ~11.4.0
* chore: [#3591711](https://git.drupalcode.org/project/webeditor/-/work_items/3591711) Update Web Editor to support Drupal ~11.4.0
* chore: [#3591726](https://git.drupalcode.org/project/webdev/-/work_items/3591726) Update Web Dev to support Drupal ~11.4.0
* chore: [#3591752](https://git.drupalcode.org/project/webconfig/-/work_items/3591752) Update Web Config to support Drupal ~11.4.0
* chore: [#3591756](https://git.drupalcode.org/project/webadmin/-/work_items/3591756) Update Web Admin to support Drupal ~11.4.0
* chore: [#3592120](https://git.drupalcode.org/project/webassets/-/work_items/3592120) Change the logo to better reflect the media library purpose

### Fixed since Webship 11.0.0-beta1:
* fix: [#3569384](https://git.drupalcode.org/project/webpage/-/work_items/3569384) Unmet configuration dependency when installing Webpage module
* fix: [#3591551](https://git.drupalcode.org/project/webblog/-/work_items/3591551) Web Blog recipe install fails on empty description/help; required field_media blocks saving; add webship-js BDD suite + CI job
* fix: [#3591611](https://git.drupalcode.org/project/webnewsletter/-/work_items/3591611) Add role-aware BDD scenarios, fix Webform CDN-library stalls via the install recipe, and modernize .gitlab-ci.yml
* fix: [#3591710](https://git.drupalcode.org/project/webeditor/-/work_items/3591710) Force Web Editor recipe to overwrite Standard profile text formats + editors, add token_filter dependency, and ship webship-js   (Playwright + Cucumber-js) functional test coverage

# 11.0.0-beta2

### Highlighted important changes
* Issue [#3532000](https://www.drupal.org/i/3532000):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Webship**
* Issue [#3531974](https://www.drupal.org/i/3531974):  
         Updated **Gin Admin Theme** from `~4.0` to `~5.0`

### Added:
* N/A

### Changed:
* Issue [#3532270](https://www.drupal.org/i/3532270):  
         Removed patch Call to a member function getEntityTypeId() on null (Layout Builder)
* Issue [#3525081](https://www.drupal.org/i/3525081):  
         Removed the **Webtheme Default Content** module from the **Webtheme** project as we moved to recipes
* Issue [#3525139](https://www.drupal.org/i/3525139):  
         Change default content for the **Webship profile** for better testing cases
* Issue [#3525138](https://www.drupal.org/i/3525138):  
         Removed the Default Content module — importing default content is in Drupal Core's Recipes API
* Issue [#3536945](https://www.drupal.org/i/3536945):  
         Change the `src_folders` path in the `nightwatch.conf.js` file

### Updates:
* Issue [#3531634](https://www.drupal.org/i/3531634):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Web Admin** module
* Issue [#3531992](https://www.drupal.org/i/3531992):  
         Updated **Gin Toolbar** from `~1.0` to `~3.0`
* Issue [#3531637](https://www.drupal.org/i/3531637):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Web Assets** module
* Issue [#3526731](https://www.drupal.org/i/3526731):  
         Updated **Media Directories** module from `~2.1.0` to `~2.2.0`
* Issue [#3531638](https://www.drupal.org/i/3531638):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Web Blog** module
* Issue [#3531640](https://www.drupal.org/i/3531640):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Web Config** module
* Issue [#3530755](https://www.drupal.org/i/3530755):  
         Updated **Diff** module from `~1.0` to `~2.0`
* Issue [#3531772](https://www.drupal.org/i/3531772):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Web Development** module
* Issue [#3531774](https://www.drupal.org/i/3531774):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Web Doc** module
* Issue [#3531776](https://www.drupal.org/i/3531776):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Web Editor** module
* Issue [#3531777](https://www.drupal.org/i/3531777):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Web Newsletter** module
* Issue [#3531779](https://www.drupal.org/i/3531779):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Web Patches** module
* Issue [#3531784](https://www.drupal.org/i/3531784):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Web Releases** module
* Issue [#3531787](https://www.drupal.org/i/3531787):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Web Security** module
* Issue [#3531791](https://www.drupal.org/i/3531791):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Web SEO** module
* Issue [#3531793](https://www.drupal.org/i/3531793):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Webpage** module
* Issue [#3531795](https://www.drupal.org/i/3531795):  
         Updated **Drupal Core** from `~11.1.0` to `~11.2.0` for **Webtheme** module
* Issue [#3535676](https://www.drupal.org/i/3535676):  
         Upgraded **Node.js** from `18.x` to `20.x` in CircleCI file

### Fixes:
* Issue [#3525086](https://www.drupal.org/i/3525086):  
         Fixed user warning: The following theme is missing from the file system: **classy**




--------

# 11.0.0-alpha1

### Highlighted important changes
* Issue [#3449844](https://www.drupal.org/i/3449844):
         Started an `11.0.x` branch for the **Webship** profile to ensure compatibility with **Drupal 11**

### Added:
* Issue [#3439036](https://www.drupal.org/i/3439036):
         Added **Web Releases** module and enable it
* Issue [#3494051](https://www.drupal.org/i/3494051):
         Added the **Webshare** module and enable it
* Issue [#3504280](https://www.drupal.org/i/3504280):
         Added `social-image.png` in the **Webtheme** theme

### Changed:
* Issue [#3382850](https://www.drupal.org/i/3382850):
         Changed team size in contact page from text to select form
* Issue [#3518460](https://www.drupal.org/i/3518460):
         Removed Admin Toolbar module from **Webadmin** module in **Drupal 10** and **11**
* Issue [#3518445](https://www.drupal.org/i/3518445):
         Removed enabling **Admin Toolbar Search** module
* Issue [#3517535](https://www.drupal.org/i/3517535):
         Removed enabling Dashboards statistic module
* Issue [#3512753](https://www.drupal.org/i/3512753):
         Changed **Folder Structure** and **File Names** for Automated Testing Feature Files for Webship
* Issue [#3511403](https://www.drupal.org/i/3511403):
         Changed admin login webship testing feature
* Issue [#3517528](https://www.drupal.org/i/3517528):
         Removed leftover field product name
* Issue [#3449837](https://www.drupal.org/i/3449837):
         Started an `11.0.x` branch for the **Webtheme Admin** theme to ensure compatibility with **Drupal 11**
* Issue [#3449834](https://www.drupal.org/i/3449834):
         Started an `11.0.x` branch for the **Webtheme** theme to ensure compatibility with **Drupal 11**
* Issue [#3449842](https://www.drupal.org/i/3449842):
         Started an `11.0.x` branch for the **Webship Default Content module** to ensure compatibility with **Drupal 11**
* Issue [#3449828](https://www.drupal.org/i/3449828):
         Started an `11.0.x` branch for the **Webtheme Default Content** module to ensure compatibility with **Drupal 11**
* Issue [#3449820](https://www.drupal.org/i/3449820):
         Started an `11.0.x` branch for the **Webpage** module to ensure compatibility with **Drupal 11**
* Issue [#3449810](https://www.drupal.org/i/3449810):
         Started an `11.0.x` branch for the **Web SEO** module to ensure compatibility with **Drupal 11**
* Issue [#3449805](https://www.drupal.org/i/3449805):
         Started an `11.0.x` branch for the **Web Security** module to ensure compatibility with **Drupal 11**
* Issue [#3449797](https://www.drupal.org/i/3449797):
         Started an `11.0.x` branch for the **Web Releases** module to ensure compatibility with **Drupal 11**
* Issue [#3447997](https://www.drupal.org/i/3447997):
         Started an `11.0.x` branch for the **Web Patches** module to ensure compatibility with **Drupal 11**
* Issue [#3449779](https://www.drupal.org/i/3449779):
         Started an `11.0.x` branch for the **Web Newsletter** module to ensure compatibility with **Drupal 11**
* Issue [#3449766](https://www.drupal.org/i/3449766):
         Started an `11.0.x` branch for the **Web Editor** module to ensure compatibility with **Drupal 11**
* Issue [#3449754](https://www.drupal.or/ig/3449754):
         Started an `11.0.x` branch for the **Web Doc** module to ensure compatibility with **Drupal 11**
* Issue [#3448039](https://www.drupal.org/i/3448039):
         Started an `11.0.x` branch for the **Web Development** module to ensure compatibility with **Drupal 11**
* Issue [#3448024](https://www.drupal.org/i/3448024):
         Started an `11.0.x` branch for the **Web Blog** module to ensure compatibility with **Drupal 11**
* Issue [#3448009](https://www.drupal.org/i/3448009):
         Started an `11.0.x` branch for the **Web Assets** module to ensure compatibility with **Drupal 11**
* Issue [#3448004](https://www.drupal.org/i/3448004):
         Started an `11.0.x` branch for the **Web Admin** module to ensure compatibility with **Drupal 11**

### Updates:
* Issue [#3492384](https://www.drupal.org/i/3492384):
         Updated **Drush from `~12` to `~13`
* Issue [#3385781](https://www.drupal.org/i/3385781):
         Updated **google chrome browser** , **chrome driver** and **Selenium**
* Issue [#3492385](https://www.drupal.org/i/3492385):
         Updated **Webformv module from `~6.2.0` to `~6.3.0`
* Issue [#3520953](https://www.drupal.org/i/3520953):
         Updated **Field Group** module from `~3.0` to `~4.0`
* Issue [#3487160](https://www.drupal.org/i/3487160):
         Updated **Metatag** module from `~2.0.0` to `~2.1.0`
* Issue [#3492398](https://www.drupal.org/i/3492398):
         Updated **Better Exposed Filters** from `~6.0` to `~7.0`

### Fixes:
* Issue [#3510470](https://www.drupal.org/i/3510470):
         Fixed CircleCI report file to work with the latest versions