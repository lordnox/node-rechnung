

buffertools = require 'buffertools'
{spawn} = require 'child_process'

config =
  exe: 'php'

setConfiguration = (key, value) ->
  config[key] = value
getConfiguration = (key) ->
  if key then config[key] else config

rechnung = (data, settings, fn) ->
  if(typeof settings is 'function')
    fn        = settings
    settings  = data.settings

  done    = false
  _out    = new buffertools.WritableBufferStream
  _err    = new buffertools.WritableBufferStream
  start   = +new Date

  exe = config.exe
  log = config.log

  args = JSON.stringify settings: settings, data: data

  finish  = (err, result) ->
    return if done
    done = true
    fn err, result, (+new Date) - start

  prog = spawn exe, [__dirname + '/../php/pdf.php']
  prog.stdin.end args

  prog.on 'exit', (code, signal) ->
    if code
      #return finish code, _err.getBuffer() if _err.length
      return finish code, _err.getBuffer()

    finish code, _out.getBuffer()

  prog.on 'error', (err) ->
    finish err, _err.getBuffer()

  prog.stdout.pipe _out
  prog.stderr.pipe _err

  prog


module.exports = rechnung

