/// <reference types="flarum/@types/translator-icu-rich" />
import Modal, { IInternalModalAttrs } from 'flarum/common/components/Modal';
import Stream from 'flarum/common/utils/Stream';
import type Mithril from 'mithril';
import User from 'flarum/common/models/User';
export interface ModeratorNotesCreateAttrs extends IInternalModalAttrs {
    user?: User;
    callback?: () => void;
}
export default class ModeratorNotesCreate extends Modal<ModeratorNotesCreateAttrs> {
    noteContent: Stream<string>;
    user?: User;
    oninit(vnode: Mithril.Vnode<ModeratorNotesCreateAttrs>): void;
    className(): string;
    title(): import("@askvortsov/rich-icu-message-formatter").NestedStringArray;
    content(): JSX.Element;
    onsubmit(e: Event): void;
    onerror(error: any): void;
}
