

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

data =
  adresse: [
      "KSS services/solutions"
      "Mörkenstraße 49"
      "22767 Hamburg"
    ]
  rechnungsnummer: '2013-08-02'
  datum: 'Hamburg, den 9. August 2013'
  inklusive: true
  einheiten: [
    datum: '29.04.2013'
    text: 'Webseiten Entwicklung (RTA)'
    menge: '7:41'
    preis: 25
  ,
    datum: '07.05.2013'
    text: 'Webseiten Entwicklung (RTA)'
    menge: '8:25'
    preis: 25
  ,
    datum: '08.05.2013'
    text: 'Webseiten Entwicklung (RTA)'
    menge: '3:22'
    preis: 25
  ,
    datum: '13.05.2013'
    text: 'Webseiten Entwicklung (vejas)'
    menge: '5:58'
    preis: 25
  ,
    datum: '14.05.2013'
    text: 'Webseiten Entwicklung (RTA)'
    menge: '2:24'
    preis: 25
  ,
    datum: '28.05.2013'
    text: 'Webseiten Entwicklung (RTA)'
    menge: '5:38'
    preis: 25
  ]

Rechnung data, settings, (err, data) ->
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



