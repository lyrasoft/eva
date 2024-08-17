
export function prepareList<T = object>(items: T[], defaultData: Record<string, any> = {}, uidField = 'uid') {
  return items.map(item => prepareListItem(item, defaultData || {}, uidField));
}

export function prepareListItem<T = object>(item: T, defaultData: Record<string, any> = {}, uidField = 'uid') {
  return {
    ...{ [uidField]: u.uid() },
    ...defaultData,
    ...item,
  };
}
