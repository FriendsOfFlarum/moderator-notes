import Model from 'flarum/common/Model';
import User from 'flarum/common/models/User';

export default class ModeratorNote extends Model {
  note() {
    return Model.attribute<string>('note');
  }

  createdAt() {
    return Model.attribute('createdAt', Model.transformDate);
  }

  addedByUser() {
    return Model.hasOne<User>('addedByUser');
  }
}
