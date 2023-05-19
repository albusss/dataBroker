const { createLogger, format, transports } = require('winston');
const { combine, timestamp, label, printf } = format;

const myFormat = printf(({ level, message, timestamp }) => {
  return `${timestamp} [${level}] ${message}`;
});

const logger = createLogger({
  format: combine(
    timestamp(),
    myFormat
  ),
  transports: [new transports.File({ filename: 'parser-logs.log' })]
});

module.exports = logger;