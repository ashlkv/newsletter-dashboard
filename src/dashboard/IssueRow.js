import React from 'react';
import Auth from './../Auth';

export default class IssueRow extends React.Component {
    constructor(props) {
        super(props);
    }

    onClick(e) {
        e.preventDefault();
    }

    render() {
        // TODO Include year if year is different.
        let dateFormatted = this.props.timestamp ? moment(this.props.timestamp * 1000).format('D MMMM') : null;
        let link = Auth.addTokenToQuery(this.props.link);
        return (
            <tr className="issue-row">
                <td className="issue-date">{dateFormatted}</td>
                <td>
                    <header className="issue-row-header">
                        <a href={link} target="_blank">
                            #{this.props.number} — {this.props.subject}
                        </a>
                    {/* <a className="btn btn-default issue-share" role="button" onClick={this.onClick.bind(this)}>Поделиться</a> */}
                    </header>
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
