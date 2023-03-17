const fileUtil = (appContext) => {
    return {
        headersWithRows: parseHeadersAndRows(appContext)
    };
}

export default fileUtil;