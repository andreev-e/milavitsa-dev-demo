import Resource from '@/api/resource';

class MailingTemplateResource extends Resource {
  constructor() {
    super('mailing_emailtemplate');
  }
}

export { MailingTemplateResource as default };
