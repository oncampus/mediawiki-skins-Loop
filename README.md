# mediawiki-skins-Loop
Skin for the Learning Object Online Platform

## Willkommen bei LOOP

LOOP ist die neue Autorensoftware der Technischen Hochschule Lübeck. Die Abkürzung steht für Learning Object Online Platform. 

LOOP findet man unter [eduloop.de](https://eduloop.de)

### Bestandteile

Der Skin enthält folgende Third-Party Packages:
- Bootstrap 4.1 (via Composer)
- JSTree https://github.com/oncampus/jstree (Submodule)<br>
- JS-Mediaplayer "plyr" https://github.com/oncampus/plyr (Submodule)<br><br>

Zur Installation der Submodule folgenden git command unter mediawiki/skins/Loop ausführen:<br>
<code>git submodule init</code><br>
<code>git submodule update</code>

Zur Installation der Composer packages den folgenden command unter mediawiki/skins/Loop ausführen:<br>
<code>composer update --no-dev</code>

#### Mehr Informationen

LOOP besteht aus
- diesem Skin,
- einigen Core-Änderungen: https://github.com/oncampus/mediawiki
- einer Extention https://github.com/oncampus/mediawiki-extensions-Loop
