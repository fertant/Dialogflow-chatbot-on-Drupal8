import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map } from 'rxjs/operators';
import { MessageModel } from '../model/message';
import { NgxSpinnerService } from 'ngx-spinner';

@Injectable()
export class MessagesService {

  messages: Array<MessageModel>;

  constructor(
    private http: HttpClient,
    private spinner: NgxSpinnerService
  ) { }

  getMessages(message: MessageModel) {
    this.spinner.show();
    const url = 'http://drupal.docker.localhost:8000/chatbot/get-intents?';
    const attr = [
      `_fomat=json`,
      `session=${message.session}`,
      `message=${message.message}`
    ];

    return this.http.get(url + attr.join('&'))
      .pipe(
        map(res => {
          this.spinner.hide();
          return Object.values(res).map(item => {
            return new MessageModel(
              'chatbot',
              item.message,
              item.session
            );
          });
        }, error => {
          this.spinner.hide();
        })
      );
  }
}
