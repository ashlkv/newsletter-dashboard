let React = require('react');

export default class IssueRow extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        // TODO Include year if year is different.
        let dateFormatted = moment(this.props.date).format('D MMMM');
        return (
            <tr>
                <td className="issue-date">{dateFormatted}</td>
                <td>
                    <a href="{this.props.link}">
                        #{this.props.number} — {this.props.title}
                    </a>
                </td>
            </tr>
        );
    }
}

IssueRow.propTypes = {
    date: React.PropTypes.instanceOf(Date),
    number: React.PropTypes.number,
    title: React.PropTypes.string,
    link: React.PropTypes.string
};
