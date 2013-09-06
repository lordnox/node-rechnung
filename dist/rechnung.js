var buffertools, config, getConfiguration, rechnung, setConfiguration, spawn;

buffertools = require('buffertools');

spawn = require('child_process').spawn;

config = {
  exe: 'php'
};

setConfiguration = function(key, value) {
  return config[key] = value;
};

getConfiguration = function(key) {
  if (key) {
    return config[key];
  } else {
    return config;
  }
};

rechnung = function(data, settings, fn) {
  var args, done, exe, finish, log, prog, start, _err, _out;

  done = false;
  _out = new buffertools.WritableBufferStream;
  _err = new buffertools.WritableBufferStream;
  start = +(new Date);
  exe = config.exe;
  log = config.log;
  args = JSON.stringify({
    settings: settings,
    data: data
  });
  finish = function(err, result) {
    if (done) {
      return;
    }
    done = true;
    return fn(err, result, (+(new Date)) - start);
  };
  prog = spawn(exe, [__dirname + '/../php/pdf.php']);
  prog.stdin.end(args);
  prog.on('exit', function(code, signal) {
    if (code) {
      return finish(code, _err.getBuffer());
    }
    return finish(code, _out.getBuffer());
  });
  prog.on('error', function(err) {
    return finish(err, _err.getBuffer());
  });
  prog.stdout.pipe(_out);
  prog.stderr.pipe(_err);
  return prog;
};

module.exports = rechnung;

/*
//@ sourceMappingURL=rechnung.js.map
*/