import Component, { ComponentAttrs } from 'flarum/common/Component';
import ItemList from 'flarum/common/utils/ItemList';
import ModeratorNote from '../model/ModeratorNote';
import type Mithril from 'mithril';
export interface NoteListItemAttrs extends ComponentAttrs {
    note: ModeratorNote;
    onDelete: () => void;
}
export default class NoteListItem extends Component<NoteListItemAttrs> {
    view(): JSX.Element;
    noteActions(context: ModeratorNote): ItemList<Mithril.Children>;
    deleteNote(note: ModeratorNote): Promise<void>;
}
