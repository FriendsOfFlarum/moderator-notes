import { IFormModalAttrs } from 'flarum/common/components/FormModal';
import FormModal from 'flarum/common/components/FormModal';
import Stream from 'flarum/common/utils/Stream';
import type Mithril from 'mithril';
import User from 'flarum/common/models/User';
export interface ModeratorNotesCreateAttrs extends IFormModalAttrs {
    user?: User;
    callback?: () => void;
}
export default class ModeratorNotesCreate extends FormModal<ModeratorNotesCreateAttrs> {
    noteContent: Stream<string>;
    user?: User;
    oninit(vnode: Mithril.Vnode<ModeratorNotesCreateAttrs>): void;
    className(): string;
    title(): string | any[];
    content(): JSX.Element;
    onsubmit(e: Event): void;
    onerror(error: any): void;
}
