require('bootstrap/dist/css/bootstrap.min.css');

let React = require('react');
let ReactDOM = require('react-dom');
global.moment = require('moment');

import App from './App';

moment.locale('ru');

ReactDOM.render(<App />, document.getElementById('root'));
