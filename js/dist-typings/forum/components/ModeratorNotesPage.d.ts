import UserPage from 'flarum/forum/components/UserPage';
import type Mithril from 'mithril';
export default class ModeratorNotesPage extends UserPage {
    oninit(vnode: Mithril.Vnode): void;
    content(): JSX.Element;
}
