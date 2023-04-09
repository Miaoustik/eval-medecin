import {useCallback, useMemo, useState} from "react";
import {capitalizeFirstLetter} from "../Utils/functions";

export default function (
    patientOnlyProps,
    titleProps,
    descriptionProps,
    allergensProps,
    dietsProps,
    prepsProps,
    ingredientsProps,
    stageProps,
    controllerRef,
    errorRef,
    recipeId,
    setRefresh,
    recipeid = null,
    setRecipeId = null,
) {

    const [postLoading, setPostLoading] = useState(false)
    const [trySubmitted, setTrySubmitted] = useState(false)
    const [submitted, setSubmitted] = useState(false)

    const handleTrySubmit = useCallback((e) => {
        e.preventDefault()
        setTrySubmitted(true)
        setSubmitted(false)
    }, [])

    const handleTryNo = useCallback(() => {
        setTrySubmitted(false)
    }, [])

    const handleResetForm = useCallback(() => {
        setRefresh(prevState => !prevState)
        titleProps.setState({
            value: '',
            error: ''
        })
        descriptionProps.setState({
            value: '',
            error: ''
        })

        prepsProps.setState({
            "preparation": "",
            "cuisson": "",
            "repos": ""
        })

        ingredientsProps.setState({
            id: 1,
            quantity: '',
            name: '',
            error: ''
        })

        stageProps.setState({
            id: 1,
            value: '',
            error: '',
            nameId: 'stage'
        })
    }, [])


    const handleTryYes = useCallback((e) => {
        e.preventDefault()

        setSubmitted(false)
        if (errorRef.current === 0) {
            setPostLoading(true)
            function setAllergensDiets (entityProps) {
                const newArray = []
                Object.entries(entityProps.state).forEach(v => {
                    if (v[1]) {
                        newArray.push(v[0])
                    }
                })
                return newArray
            }

            const recipeObj = {
                title: capitalizeFirstLetter(titleProps.value.trim()),
                description: capitalizeFirstLetter(descriptionProps.value.trim()),
                allergens: setAllergensDiets(allergensProps),
                diets: setAllergensDiets(dietsProps),
                repos: prepsProps.state.repos === '' ? 0 : parseInt(prepsProps.state.repos, 10),
                cuisson: prepsProps.state.cuisson === '' ? 0 : parseInt(prepsProps.state.cuisson, 10),
                preparation: prepsProps.state.preparation === '' ? 0 : parseInt(prepsProps.state.preparation, 10),
                patientOnly: patientOnlyProps.state,
                ingredients: ingredientsProps.state.map(e => {
                    return {
                        quantity: e.quantity,
                        name: capitalizeFirstLetter(e.name.trim())
                    }
                }),
                stages: stageProps.state.map(e => {
                    return e.value
                })
            }

            if (recipeId) {
                recipeObj['id'] = recipeId
            }

            const fetchOptions = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(recipeObj),
                signal: controllerRef.current.signal
            }

            const url = recipeid ? '/admin/api/modifier-recette/' + recipeid + '/modify' : '/admin/api/creer-recette/create'

            fetch(url, fetchOptions)
                .then(response => response.json())
                .then(data => setRecipeId(data))
                .finally(() => {
                    setSubmitted(true)
                    setPostLoading(false)

                })
        } else {
            setTrySubmitted(false)
        }
    }, [
        titleProps,
        descriptionProps,
        allergensProps,
        dietsProps,
        prepsProps,
        ingredientsProps,
        stageProps,
        errorRef
    ])

    return useMemo(() => ({
        handleTryYes,
        postLoading,
        trySubmitted,
        submitted,
        handleTrySubmit,
        handleTryNo,
        handleResetForm
    }), [
        handleTryYes,
        postLoading,
        trySubmitted,
        submitted,
        handleTrySubmit,
        handleTryNo,
        handleResetForm
    ])
}