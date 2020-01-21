UKMresponsive
=============
Design for UKM Norges nettsteder. Kjører både på wordpress og symfony out-of-the-box

## Symfony
Installer med composer "ukmnorge/designbundle"
```json
   "repositories":[
	    {
            "url": "https://github.com/UKMNorge/UKMresponsive.git",
            "type": "git"
        }
    ],
    "require": {
        "ukmnorge/designbundle": "dev-master"
    },
``` 

### Config.yml må ha disse linjene:
```
globals:
    UKM_HOSTNAME: %UKM_HOSTNAME%
    SEO: "@ukmdesign.seo"
    Sitemap: "@ukmdesign.sitemap"
    THEME: "%UKMresponsive.theme%"
    section: "%UKMresponsive.section%"
```

### Parameters.yml må ha disse linjene:
```
UKMresponsive.theme: ''
UKMresponsive.section:
    title: UKM RSVP
    url: //rsvp.ukm.no

SEOdefaultsApp:
    title: UKM RSVP
    canonical: //rsvp.ukm.no
    description: Kommer du?
    siteName: UKM RSVP
    section: organisasjon
    description: Kommer du?
```

### Sitemap auto-update
Det er viktig at sitemap til enhver tid er synkronisert mellom alle installasjoner.
Sett derfor opp en cron-jobb som åpner APP_URL/cron/designbundle/sync_sitemap/ så ofte som ønskelig (anbefalt minst 1 gang per døgn)
Cron-jobben skriver så sitemap.yml som funnet på GitHub til en lokal cache uten nedetid. Skulle henting feile, bruker den sitemap.yml fra installasjonstidspunktet.

## Wordpress
Klon repoet inn i wp-content/themes
### Sitemap auto-update
Sett opp GitHub-hook og auto-pull temaet for hver push

## Standalone
Noen ganger er det greit med design, twig og alt det der for enkle script. Da kan standalone brukes.

*OBS: per nå auto-oppdateres ikke sitemap, som betyr at menyen kan være feil*

### For å bruke designbundle, kreves følgende kode-linjer
(Loaderen vil varsle om manglende konstanter)
```php
define('STANDALONE_URL', 'https://url-til-script');
define('STANDALONE_AJAX', 'https://url-til-ajax-endpoint');
define('STANDALONE_SECTION', 'Navn på overordnet seksjon (ungdom,organisasjonen osv)');
define('STANDALONE_TITLE', 'Navn på siden');
define('STANDALONE_DESCRIPTION', 'Beskrivelse av siden');
define('STANDALONE_TWIG_PATH', dirname(__FILE__) . '/twig/');

require_once('vendor/autoload.php');
require_once('vendor/ukmnorge/designbundle/UKMNorge/Standalone/Environment/loader.php');
```
Det kan være smart å definere cache-path for lagring av twig-templates
```php
WP_TWIG::setCacheDir( dirname(__FILE__).'/cache/' );
```

For å sende med data og rendre
OBS: Bruk `$WP_TWIG_DATA` da denne også brukes av loaderen.
```php
$WP_TWIG_DATA[ key ] = [ value ];
echo WP_TWIG::render( 'Folder/Template', $WP_TWIG_DATA );
```