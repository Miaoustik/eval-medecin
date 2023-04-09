import {useEffect, useMemo, useState} from "react";

export default function (url, controllerRef) {

    const [data, setData] = useState(null)
    const [loading, setLoading] = useState(true)
    const [refresh, setRefresh] = useState(false)


    useEffect(() => {

        setLoading(true)


        const fetchOptions = {
            headers: {
                Accept: 'application/json',
            },
            signal: controllerRef.current.signal
        }


        fetch(url, fetchOptions)
            .then(res => res.json())
            .then(dataRes => {
                setData(dataRes)
            })
            .catch((e) => console.warn(e))
            .finally(() => {
                setLoading(false)
            })

    }, [refresh])




    return useMemo(() => ({
        data,
        loading,
        setRefresh
    }), [
        data,
        loading
    ])
}