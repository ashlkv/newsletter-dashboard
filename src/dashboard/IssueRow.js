import React from 'react';
import Auth from './../Auth';

export default class IssueRow extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        // TODO Include year if year is different.
        let dateFormatted = this.props.timestamp ? moment(this.props.timestamp * 1000).format('D MMMM') : null;
        let link = Auth.addTokenToQuery(this.props.link);
        return (
            <tr>
                <td className="issue-date">{dateFormatted}</td>
                <td>
                    <a href={link}>
                        #{this.props.number} — {this.props.subject}
                    </a>
                </td>
            </tr>
        );
    }
}

IssueRow.propTypes = {
    id: React.PropTypes.string,
    timestamp: React.PropTypes.number,
    number: React.PropTypes.number,
    title: React.PropTypes.string,
    subject: React.PropTypes.string,
    link: React.PropTypes.string
};
