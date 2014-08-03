

Rechnung = require './rechnung'
require 'colors'
theme = require '../../node-rechnung-theme/raynode'

util = require 'util'
log = (title, color) ->
  title = "#{title}:"[color]
  (msg) ->
    if typeof msg isnt 'string'
      msg = util.inspect msg, depth: 1
    console.log title + " #{msg}"


logBuild  = log 'build', 'magenta'
logPHP    = log 'php', 'green'

settings =
  theme: theme

time2money = (minuten, preis) ->
  Math.round(100 * minuten / 60) * preis / 100

renderTime = (minuten) ->


data =
  adresse: [
      "DIS Interim Management GmbH"
      "c/o DIS AG IT"
      "Markgrafenstr. 33"
      "10117 Berlin"
    ]
  rechnungsnummer: 'RN: 2013-01-01   PN: 1-508-142-01-2013'
  datum: 'Hamburg, den 30. Januar 2014'
  inklusive: true
  einheiten: [
    datum: "07.01.14"
    menge: "8:37"
    text:  "Erzeugen der Grundstrukturen für die Benutzerverwaltung"
    preis: 65
  ,
    datum: "08.01.14"
    menge: "9:21"
    text:  "Aufarbeiten einiger Probleme mit dem vorhandenen Ticketsystem"
    preis: 65
  ,
    datum: "10.01.14"
    menge: "6:58"
    text:  "Erzeugen einer Box-Komponente, Evaluation eines Grids"
    preis: 65
  ,
    datum: "13.01.14"
    menge: "10:07"
    text:  "Übernahme von Grid-Codes aus einem alten Projekt"
    preis: 65
  ,
    datum: "14.01.14"
    menge: "10:19"
    text:  "Kleiner Fehlerbehebungen und Designanpassungen"
    preis: 65
  ,
    datum: "15.01.14"
    menge: "10:55"
    text:  "Kleiner Fehlerbehebungen und Designanpassungen"
    preis: 65
  ,
    datum: "17.01.14"
    menge: "7:25"
    text:  "Zerlegen des Grids in Pagination und Grid, Paginations Tests geschrieben"
    preis: 65
  ,
    datum: "20.01.14"
    menge: "10:44"
    text:  "Feuerwehr, Anpassung des Ticketsystems an neue Vorgaben"
    preis: 65
  ,
    datum: "21.01.14"
    menge: "9:18"
    text:  "Feuerwehr, Anpassung um die Paginierung aus den Zuständen zu entfernen"
    preis: 65
  ,
    datum: "22.01.14"
    menge: "7:13"
    text:  "Fertigstellung des Grids, inklusive Filterung und Sortierung Serverseitig"
    preis: 65
  ,
    datum: "24.01.14"
    menge: "3:14"
    text:  "Grid Tests geschrieben, Popup-Grundlage"
    preis: 65
  ,
    datum: "27.01.14"
    menge: "8:55"
    text:  "Popup-Service inkl. 100% CC, Block-Service zum abweisen von Formularverlusten"
    preis: 65
  ,
    datum: "28.01.14"
    menge: "8:59"
    text:  "Diverse kleinere Anpassungen, kleine Dokumentation über kx-utilities, Testing"
    preis: 65
  ,
    datum: "29.01.14"
    menge: "8:00"
    text:  "Große Übergabe und letzte Fehlerbehebungen"
    preis: 65
  ].map (einheit) ->
    [h,m] = einheit.menge.split(":").map (i) -> parseInt(i, 10)
    einheit.minuten = h * 60 + m;
    einheit.summe = time2money einheit.minuten, einheit.preis
    if einheit.text.length > 70
      text = einheit.text.substr(0, 70)
      einheit.text = text.substr(0, text.lastIndexOf(" ")) + "..."
    einheit

data.summe = data.einheiten.reduce ((a, b) -> a + b.summe), 0
data.minuten = data.einheiten.reduce ((a, b) -> a + b.minuten), 0
data.stunden = Math.floor(data.minuten / 60)
data.zeit = Math.floor data.stunden
data.zeit += ":0" + data.minuten % 60
data.netto = data.summe;
data.brutto = data.netto * 1.19;
data.steuer = data.netto * 0.19;

small = {
  color: '#ff0000'
}

resetSmall = {
  color: '#000000'
}

settings.tablelayout =
  datum : ['Datum', 20]
  menge : ['Zeit', 55, true]
  text  : ['Leistung', 60]
  summe : ['Preis (EURO)', 200, true, ['number']]

settings.tableFooter = [data.zeit, 55, true]



data.settings = settings;

console.log data

Rechnung data, (err, data) ->
  console.log 'output length:' + data.length
  if err
    console.error 'ERROR CODE: ' + err
    console.error data.toString()
  else
    require('fs').writeFile 'test.pdf', data, (err) ->
      console.log('file `test.pdf` written')



if 0
  utils.spawner 'php', args, logPHP, (err, result) ->
    if err
      logBuild 'PHP Error:'
      return console.error result.toString 'utf8'

    fs = require 'fs'

    fs.writeFile settings.filename, result, (err) ->
      logBuild 'file created'



