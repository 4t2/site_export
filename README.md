site_export
===========

Mit dieser Erweiterung lassen sich die Artikel ganzer Seitenbäume als einzelne HTML-Dateien oder als E-Book (Epub) exportieren.

Anwendungsmöglichkeiten
-----------------------

* Seiten für die Offline-Nutzung exportieren
* Seiten für die Nutzung in anderen Anwendungen (bspw. mobile Apps für iPhone und Co.) exportieren
* Epub E-Books von bestehenden Seiten erzeugen

Denkbar ist auch der Einsatz als E-Book-Publishingsystem. Durch die ausgefeilte Rechteverwaltung von Contao können ja verschiedene Autoren an ihren Artikel/Kapiteln eines Buchs gemeinsam arbeiten und dieses Buch lässt sich dann zentral als Epub exportieren.

Export-Set
----------

Im Export-Set definiert man das Export-Verzeichnis und wählt die zu exportierenden Seiten aus. Für jedes Export-Set lässt sich ein extra Layout definieren, so dass die Seiten unabhängig vom Webseitenlayout frei exportiert werden können. Auf Wunsch wird automatisch ein eingerücktes oder flaches Inhaltsverzeichnis (toc.html) erstellt. Dabei wird die Struktur komprimiert, so dass keine Lücken entstehen, wenn in der Seitenstruktur nicht alle Ebenen enthalten sind.

EPUB
----

Soll aus den exportieren Seiten ein EPUB erzeugt werden, können dazu ein Cover-Bild und weitere notwendige Informationen wie bspw. Titel, Beschreibung, ID, Autor oder Sprache definiert werden.

Wichtig ist, dass dazu auf jeden Fall ein extra Layout angelegt und eingebunden wird, welches der [EPUB-Definition](http://idpf.org/epub) entspricht.

Ersetzungsregeln
----------------

Zu jedem Export-Set können beliebig viel Export-Ersetzungsregeln angelegt werden, mit denen die Seiten vor dem Export noch angepasst werden können. Die Regeln können einfache Ersetzungen oder reguläre Ausdrücke enthalten. So können damit zum Beispiel bestimmte Inhaltselemente entfernt werden, die nicht mit im Export enthalten sein sollen.

Export
------

Beim Export werden im ersten Schritt die zu exportierenden Seiten angezeigt. Im zweiten Schritt werden schon vorhandene Dateien im Export-Verzeichnis gelöscht und die ausgewählten Seiten exportiert.

Die Seiten werden alle ins Export-Verzeichnis geschrieben und die Dateinamen werden aus dem Alias generiert. Eingebundene Audio-Dateien, Bilder und Stylesheets werden automatisch mit exportiert und die Verweise werden angepasst. Auch werden interne Links so weit wie möglich an die lokale Struktur angepasst.

Bei Bedarf wird ein HTML- und/oder JSON-Inhaltsverzeichnis generiert.
