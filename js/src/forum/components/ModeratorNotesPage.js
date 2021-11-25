import UserPage from 'flarum/forum/components/UserPage';
import ModeratorNotes from './ModeratorNotes';

export default class ModeratorNotesPage extends UserPage {
  oninit(vnode) {
    super.oninit(vnode);
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
