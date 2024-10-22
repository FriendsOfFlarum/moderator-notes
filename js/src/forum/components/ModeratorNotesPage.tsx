import UserPage from 'flarum/forum/components/UserPage';
import ModeratorNotes from './ModeratorNotes';
import type Mithril from 'mithril';

export default class ModeratorNotesPage extends UserPage {
  oninit(vnode: Mithril.Vnode) {
    super.oninit(vnode);

    this.loadUser(m.route.param('username'));
  }

  content() {
    return (
      <div className="DiscussionsUserPage">
        <ModeratorNotes user={this.user} sort="sort" />
      </div>
    );
  }
}
