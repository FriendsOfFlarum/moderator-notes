import UserPage from 'flarum/components/UserPage';
import ModeratorNotes from './ModeratorNotes';

export default class ModeratorNotesPage extends UserPage {
    oninit(vdom) {
        super.oninit(vdom);
        this.loadUser(m.route.param('username'));
    }

    content() {
        return (
            <div className="DiscussionsUserPage">
                {ModeratorNotes.component({
                    params: {
                        user: this.user,
                        sort: 'newest',
                    },
                })}
            </div>
        );
    }
}
