var Rechnung, data, log, logBuild, logPHP, settings, theme, util;

Rechnung = require('./rechnung');

require('colors');

theme = require('../../node-rechnung-theme/raynode');

util = require('util');

log = function(title, color) {
  title = ("" + title + ":")[color];
  return function(msg) {
    if (typeof msg !== 'string') {
      msg = util.inspect(msg, {
        depth: 1
      });
    }
    return console.log(title + (" " + msg));
  };
};

logBuild = log('build', 'magenta');

logPHP = log('php', 'green');

settings = {
  theme: theme
};

data = {
  adresse: ["KSS services/solutions", "Mörkenstraße 49", "22767 Hamburg"],
  rechnungsnummer: '2013-08-02',
  datum: 'Hamburg, den 9. August 2013',
  inklusive: true,
  einheiten: [
    {
      datum: '29.04.2013',
      text: 'Webseiten Entwicklung (RTA)',
      menge: '7:41',
      preis: 25
    }, {
      datum: '07.05.2013',
      text: 'Webseiten Entwicklung (RTA)',
      menge: '8:25',
      preis: 25
    }, {
      datum: '08.05.2013',
      text: 'Webseiten Entwicklung (RTA)',
      menge: '3:22',
      preis: 25
    }, {
      datum: '13.05.2013',
      text: 'Webseiten Entwicklung (vejas)',
      menge: '5:58',
      preis: 25
    }, {
      datum: '14.05.2013',
      text: 'Webseiten Entwicklung (RTA)',
      menge: '2:24',
      preis: 25
    }, {
      datum: '28.05.2013',
      text: 'Webseiten Entwicklung (RTA)',
      menge: '5:38',
      preis: 25
    }
  ]
};

Rechnung(data, settings, function(err, data) {
  console.log('output length:' + data.length);
  if (err) {
    console.error('ERROR CODE: ' + err);
    return console.error(data.toString());
  } else {
    return require('fs').writeFile('test.pdf', data, function(err) {
      return console.log('file `test.pdf` written');
    });
  }
});

if (0) {
  utils.spawner('php', args, logPHP, function(err, result) {
    var fs;

    if (err) {
      logBuild('PHP Error:');
      return console.error(result.toString('utf8'));
    }
    fs = require('fs');
    return fs.writeFile(settings.filename, result, function(err) {
      return logBuild('file created');
    });
  });
}

/*
//@ sourceMappingURL=example.js.map
*/