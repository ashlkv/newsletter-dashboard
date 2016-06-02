import React from 'react';

import './app.sass';

export default class App extends React.Component {
    render() {
        return (
            <div className="app">
                {this.props.children}
            </div>
        )
    }
}
