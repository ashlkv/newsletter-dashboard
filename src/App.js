let React = require('react');

import PreviousIssues from './previous-issues/PreviousIssues';
import './app.sass';

export default
class App extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                issues: []
            }
        };
    }

    componentDidMount() {
        $.ajax({
            url: '/backend/dashboard',
            dataType: 'json',
            cache: false,
            success: function(response) {
                this.setState({data: response.data});
            }.bind(this),
            error: function() {
                console.error('Не могу получить данные');
            }.bind(this)
        });
    }

    render() {
        let data = {issues: this.state.data.issues};
        return (
            <div className="app">
                <h1>Личный кабинет</h1>
                <PreviousIssues {...data}/>
            </div>
        );
    }
}
