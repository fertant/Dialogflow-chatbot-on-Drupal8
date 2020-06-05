import { Component, OnInit, ChangeDetectionStrategy, Input } from '@angular/core';
import { MessageModel } from '../../shared/model/message';

@Component({
  selector: 'app-message',
  templateUrl: './message.component.html',
  styleUrls: ['./message.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class MessageComponent implements OnInit {

  @Input() message: MessageModel;

  constructor() { }

  ngOnInit(): void {
  }

}
