export default function (context) {
  if (context.route?.meta[0]?.hasAuth) {
    if (context.$auth?.$state?.user != false) {
      if(typeof context.route?.meta[0]?.scope === 'string'){
        if(!context.$auth?.$state?.user?.scopes?.includes(context.route?.meta[0]?.scope))
        context.error({ statusCode: 401 });
      }else{
        const len = context.route?.meta[0]?.scope.length;
        let hasScope = false;
        for (let index = 0; index < len; index++) {
          if (context.$auth?.$state?.user?.scopes?.includes(context.route?.meta[0]?.scope[index])) {
            hasScope = true;
          }
        }
        if(!hasScope)
        context.error({ statusCode: 401 });
      }
    }
  }
}
