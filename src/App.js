let React = require('react');

import PreviousIssues from './previous-issues/PreviousIssues';
import './app.sass';

// TODO Получать через XHR-запрос
let data = {
    previousIssues: {
        issues: [
            {
                date: new Date('May 20, 1016'),
                title: 'про женский эллипсоид, мэра-самодура, спасительную типографику и умирающий Ганг',
                number: 12,
                link: ''
            },
            {
                date: new Date('May 13, 1016'),
                title: 'про афантазию, гастрономического Гитлера, занимательную футурологию и поющее мясо',
                number: 11,
                link: ''
            },
            {
                date: new Date('May 6, 1016'),
                title: 'про Филиппа Дика, кимоно, школу-лоукостер и модерирование интернета',
                number: 10,
                link: ''
            }
        ]
    }
};

export default class App extends React.Component {
    render() {
        return (
            <div className="app">
                <h1>Личный кабинет</h1>
                <PreviousIssues {...data.previousIssues}/>
            </div>
        );
    }
}
