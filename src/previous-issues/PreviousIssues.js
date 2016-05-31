let _ = require('lodash');
let React = require('react');

import IssueRow from './IssueRow';
import './previous-issues.sass';

export default class PreviousIssues extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        let issueRows = _.map(this.props.issues, (issue, key) => {
            return <IssueRow key={key} {...issue}/>;
        });

        issueRows = issueRows.slice(0, 3);

        return (
            <table className="table previous-issues">
                <caption>Последние выпуски</caption>
                <tbody>
                    {issueRows}
                </tbody>
            </table>
        )
    }
}

PreviousIssues.propTypes = {
    issues: React.PropTypes.arrayOf(React.PropTypes.object)
};
