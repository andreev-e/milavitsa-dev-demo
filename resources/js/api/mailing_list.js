import Resource from '@/api/resource';
import request from '@/utils/request';

class MailingListResource extends Resource {
  constructor() {
    super('mailing_list');
  }
}

export function Copy(id) {
  return request({
    url: '/mailing_list/copy',
    method: 'post',
    data: { id },
  });
}

export function getStatistic(id, data) {
  return request({
    url: `/mailing_list/${id}/statistic`,
    method: 'get',
    params: data,
  });
}

export { MailingListResource as default };
