import 'bootstrap/dist/css/bootstrap.min.css';

let React = require('react');
let ReactDOM = require('react-dom');

global.moment = require('moment');
moment.locale('ru');

global.$ = require('jquery');

import App from './App';

ReactDOM.render(<App />, document.getElementById('root'));
