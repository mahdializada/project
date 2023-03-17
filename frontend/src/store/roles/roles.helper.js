export const prepareRolePermissions = (rolePermissions) => {
  let newRolePermissions = {...rolePermissions};
  rolePermissions.permissions.forEach(element => {
    switch (element.name) {
      case 'Create':
        newRolePermissions.create = element.id;
        break;
      case 'Edit':
        newRolePermissions.edit = element.id;
        break;
      case 'View':
        newRolePermissions.view = element.id;
        break;
      case 'Delete':
        newRolePermissions.delete = element.id;
        break;
      default:
        break;
    }
  });
  newRolePermissions.permissions = null;
  return newRolePermissions;
}
