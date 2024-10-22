import Model from 'flarum/common/Model';
import User from 'flarum/common/models/User';

export default class ModeratorNote extends Model {
  note() {
    return Model.attribute<string>('note').call(this);
  }

  createdAt() {
    return Model.attribute('createdAt', Model.transformDate).call(this);
  }

  addedByUser() {
    return Model.hasOne<User | null>('addedByUser').call(this);
  }
}
