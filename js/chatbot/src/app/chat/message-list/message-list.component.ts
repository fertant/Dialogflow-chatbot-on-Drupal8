import { Component, OnInit } from '@angular/core';
import { NgxSpinnerService } from 'ngx-spinner';
import { Actions, ofType } from '@ngrx/effects';
import { SendAction, ReceiveAction, MessagesActionTypes } from '../../shared/store/actions/messages.actions';
import { Store } from '@ngrx/store';

import { MessageModel } from '../../shared/model/message';
import { MessagesService } from '../../shared/services/messages.service';

@Component({
  selector: 'app-message-list',
  templateUrl: './message-list.component.html',
  styleUrls: ['./message-list.component.scss']
})
export class MessageListComponent implements OnInit {

  messages: Array<MessageModel>;

  constructor(
    private spinner: NgxSpinnerService,
    private actions: Actions,
    private store: Store<any>
  ) {
    this.messages = [];
    actions.pipe(
      ofType(MessagesActionTypes.SEND)
    ).subscribe((state: SendAction) => {
      this.messages.push(state.payload);
    });
    actions.pipe(
      ofType(MessagesActionTypes.RECEIVED)
    ).subscribe((state: ReceiveAction) => {
      this.messages.push(state.payload[0]);
    });
  }

  ngOnInit(): void {
  }

}
