import Model from 'flarum/common/Model';
import User from 'flarum/common/models/User';
export default class ModeratorNote extends Model {
    note(): string;
    createdAt(): Date | null | undefined;
    addedByUser(): false | User | null;
}
