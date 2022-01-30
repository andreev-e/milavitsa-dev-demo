import Resource from '@/api/resource';

class MailingTemplateResource extends Resource {
  constructor() {
    super('mailing_template');
  }
}

export { MailingTemplateResource as default };
