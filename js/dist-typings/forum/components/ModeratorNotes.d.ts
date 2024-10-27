import Component, { ComponentAttrs } from 'flarum/common/Component';
import ItemList from 'flarum/common/utils/ItemList';
import User from 'flarum/common/models/User';
import Mithril from 'mithril';
import ModeratorNote from '../model/ModeratorNote';
import { ApiResponsePlural } from 'flarum/common/Store';
export interface ModeratorNotesAttrs extends ComponentAttrs {
    user: User;
    sort: string;
}
export default class ModeratorNotes extends Component<ModeratorNotesAttrs> {
    loading: boolean;
    notes: ModeratorNote[];
    user: User;
    loadLimit: number;
    sort: string;
    areMoreResults: boolean;
    filterByAuthor: boolean;
    oninit(vnode: Mithril.Vnode<ModeratorNotesAttrs>): void;
    view(): JSX.Element;
    sortmap(): any;
    actionItems(): ItemList<Mithril.Children>;
    setSort(sort: string): void;
    loadResults(offset?: number): Promise<void>;
    handleOnClickCreate(e: Event): void;
    moreResults(results: ApiResponsePlural<ModeratorNote>): boolean;
    handleDelete(note: ModeratorNote): Promise<void>;
}
